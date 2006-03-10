<?php
/* This file is part of the XP framework's port "Dialog"
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'util.Date',
    'util.log.Logger',
    'util.log.ConsoleAppender', 
    'de.thekid.dialog.Album',
    'de.thekid.dialog.io.ImageProcessor',
    'de.thekid.dialog.io.IndexCreator',
    'de.thekid.dialog.GroupByHourStrategy',
    'img.filter.SharpenFilter'
  );

  define('HIGHLIGHTS_MAX',    4);
  define('IMAGE_FOLDER',      dirname(__FILE__).'/converted/albums/');
  define('DATA_FOLDER',       dirname(__FILE__).'/converted/data/');
  
  // {{{ GalleryAlbum
  //     Wrapper class
  class GalleryAlbum extends Object {
    var 
      $fields= array();
    
    function getField($name, $default= NULL) {
      return isset($this->fields[$name]) ? $this->fields[$name] : $default;
    }
  }
  // }}}

  // {{{ GalleryAlbumItem
  //     Wrapper class
  class GalleryAlbumItem extends Object {
    var 
      $image            = NULL,
      $thumbnail        = NULL,
      $preview          = NULL,
      $caption          = '',
      $hidden           = FALSE,
      $highlight        = NULL,
      $highlightImage   = NULL,
      $isAlbumName      = NULL,
      $clicks           = 0,
      $keywords         = NULL,
      $comments         = NULL,
      $uploadDate       = 0,
      $itemCaptureDate  = 0,
      $exifData         = NULL,
      $owner            = '',
      $extraFields      = array();
    
    function getFileName() {
      return $this->image->name.'.'.$this->image->type;
    }
  }
  // }}}

  // {{{ GalleryImage
  //     Wrapper class
  class GalleryImage extends Object {
    var 
       $name            = '',
       $type            = '',
       $width           = 0,
       $height          = 0,
       $resizedName     = '',
       $thumb_x         = 0,
       $thumb_y         = 0,
       $thumb_width     = 0,
       $thumb_height    = 0,
       $raw_width       = 0,
       $raw_height      = 0,
       $version         = 0;
  }
  // }}}
  
  // {{{ mixed readData(&io.File) throws IOException
  //     Reads Gallery v1 data from a given file
  function readData(&$file) {
    try(); {
      $serialized= FileUtil::getContents($file);
    } if (catch('IOException', $e)) {
      return throw($e);
    }
    
    // Prepend "Gallery" to all class names to avoid name clashes
    $value= unserialize(preg_replace(
      '/O:([0-9]+):"([^"]+)"/e', 
      '"O:".($1 + 7).":\"Gallery$2\""', 
      $serialized
    ));
    
    // Check for errors from unserialize()
    if (xp::errorAt(__FILE__, __LINE__ - 3)) {
      return throw(new IOException('Data corrupted'));
    }
    
    return $value;
  }
  // }}}

  // {{{ main
  $param= &new ParamString();
  if (!$param->exists(1) || $param->exists('help', '?')) {
    Console::writeLine(<<<__
Converts Gallery v1 albums to dialog
--------------------------------------------------------------------------------
Usage:
  php gallery1.php <<gallery_root_album_folder>> [<<options>>]

Options:
  --debug, -d     Turns on debugging (default: off)
__
    );
    exit(1);
  }

  // Check origin folder
  $origin= &new Folder($param->value(1));
  if (!$origin->exists()) {
    Console::writeLinef(
      'The specified folder "%s" does not exist', 
      $origin->getURI()
    );
    exit(2);
  }

  // Check if debugging output is wanted
  $cat= NULL;
  if ($param->exists('debug')) {
    $l= &Logger::getInstance();
    $cat= &$l->getCategory();
    $cat->addAppender(new ConsoleAppender());
    $processor->setTrace($cat);
  }

  // Check for albumdb.dat
  $albumDb= &new File($origin->getURI().'albumdb.dat');
  if (!$albumDb->exists()) {
    Console::writeLinef(
      'The specified folder "%s" does not look like a Gallery v1 root folder (must contain albumdb.dat)', 
      $origin->getURI()
    );
    exit(3);
  }

  // Set up processor
  $processor= &new ImageProcessor();
  $processor->addFilter(new SharpenFilter());

  // Read albumdb.dat, it contains a list of all albums, serialized as 
  // an array of strings containing the album names
  foreach (readData($albumDb) as $albumName) {
    Console::writeLinef('===> Found album "%s"', $albumName);

    // Read album.dat and photos.dat, these should really exists, so we
    // don't do great prechecks here, rather simply catching an exception
    // and ignoring the album.
    $folder= &new Folder($origin->getURI().$albumName);
    try(); {
      $albumData= readData(new File($folder->getURI().'album.dat'));
      $photoData= readData(new File($folder->getURI().'photos.dat'));
    } if (catch('IOException', $e)) {
      Console::writeLine('*** Seems corrupted ~ ', $e->toString());
      continue;   // Ignore this album and continue with the next
    }
    
    // DEBUG Console::writeLine(xp::stringOf($albumData));
    // DEBUG Console::writeLine(xp::stringOf($photoData));
    
    // Step #1: Create the album. 
    // Check if album already exists
    $serialized= &new File(DATA_FOLDER.$albumName.'.dat');
    if ($serialized->exists()) {
      Console::writeLine('---> Found existing album, updating');
      try(); {
        $album= unserialize(FileUtil::getContents($serialized));
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
        exit(-1);
      }
    } else {
      Console::writeLine('---> Creating new album...');
      $album= &new Album();
      $album->setName($albumName);
      $album->setTitle($albumData->getField('title', $albumName));
      $album->setCreatedAt(new Date($albumData->getField('creation_date')));
      $album->setDescription($albumData->getField('description'));
    }
    
    // Step #2: Look for highlights
    $s= sizeof($photoData);
    Console::writeLine('---> Looking for highlights in ', $s, ' pictures');
    $highlights= array();
    for ($i= 0; $i < $s; $i++) {
      $photoData[$i]->highlight && $highlights[]= $i;
    }
    $highlights && Console::writeLine('     >> Found highlights at positions #', implode(', #', $highlights));
    
    // Step #3: Complete highlights if needed
    if (sizeof($highlights) > HIGHLIGHTS_MAX) {
      Console::writeLine('     >> Too many highlights, using first ', HIGHLIGHTS_MAX);
      $highlights= array_slice($highlights, 0, HIGHLIGHTS_MAX);
    } else {
      Console::writeLine('     >> Not enough highlights, gathering ', HIGHLIGHTS_MAX - sizeof($highlights), ' more');
      for ($i= 0; $i < $s && sizeof($highlights) < HIGHLIGHTS_MAX; $i++) {
        $photoData[$i]->highlight || $highlights[]= $i;
      }
    } 

    // Step #4: Create destination folder if it doesn't exist yet
    $destination= &new Folder(IMAGE_FOLDER.$albumName);
    try(); {
      $destination->exists() || $destination->create(0755);
    } if (catch('IOException', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
    $processor->setOutputFolder($destination);

    // Step #5: Actually import the highlights
    Console::writeLine('---> Importing highlights');
    foreach ($highlights as $offset) {
      try(); {
        $highlight= &$processor->albumImageFor($folder->getURI().$photoData[$offset]->getFileName());
      } if (catch('ImagingException', $e)) {
        $e->printStackTrace();
        exit(-2);
      }

      if (!$highlight->exifData->dateTime) {
        $highlight->exifData->dateTime= &$album->getDate();
      }

      // Add to new album and 
      $album->addHighlight($highlight);
      $photoData[$offset]->highlight= TRUE;
      Console::writeLine('     >> Added highlight ', $highlight->getName());
    }
    
    // Step #6: Import the images
    Console::writeLine('---> Importing images');
    for ($i= 0; $i < $s; $i++) {
      if ($photoData[$i]->highlight) continue;
      
      try(); {
        $image= &$processor->albumImageFor($folder->getURI().$photoData[$i]->getFileName());
      } if (catch('ImagingException', $e)) {
        $e->printStackTrace();
        exit(-2);
      }

      if (!$image->exifData->dateTime) {
        $image->exifData->dateTime= &$album->getDate();
      }

      $images[]= &$image;
      Console::writeLine('     >> Added image ', $image->getName());      
    }
    
    // Step #7: Sort images by their creation date (from EXIF data)
    usort($images, create_function(
      '&$a, &$b', 
      'return $b->exifData->dateTime->compareTo($a->exifData->dateTime);'
    ));

    // Step #8: Divide up into chapters by hour
    $strategy= &new GroupByHourStrategy();
    for ($i= 0, $s= sizeof($images); $i < $s; $i++) {
      $key= $strategy->groupFor($images[$i]);
      if (!isset($chapter[$key])) {
        $chapter[$key]= &$album->addChapter(new AlbumChapter($key));
      }

      $chapter[$key]->addImage($images[$i]);
    }

    // Step #9: Save album
    Console::writeLine('---> Imported as ', xp::stringOf($album));
    try(); {
      FileUtil::setContents($serialized, serialize($album));
    } if (catch('IOException', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
    
    // Clean up
    unset($images);
    unset($highlights);
    unset($albumData);
    unset($photoData);
  }

  Console::writeLine('===> Finished at ', date('r'));
  // }}}
?>
