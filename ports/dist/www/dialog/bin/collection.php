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
    'de.thekid.dialog.EntryCollection',
    'de.thekid.dialog.Album',
    'de.thekid.dialog.Topic',
    'de.thekid.dialog.io.FilteredFolderIterator',
    'de.thekid.dialog.io.ImageProcessor',
    'de.thekid.dialog.io.IndexCreator',
    'de.thekid.dialog.GroupByHourStrategy',
    'img.filter.ConvolveFilter'
  );

  define('DESCRIPTION_FILE',  'description.txt');
  define('TITLE_FILE',        'title.txt');
  define('HIGHLIGHTS_FOLDER', 'highlights');
  define('HIGHLIGHTS_MAX',    5);
  define('ENTRIES_PER_PAGE',  5);
  define('FOLDER_FILTER',     '/\.jpe?g$/i');

  define('DATA_FOLDER',       dirname(__FILE__).'/../data/');
  define('IMAGE_FOLDER',      dirname(__FILE__).'/../doc_root/albums/');
  
  // {{{ main
  $param= new ParamString();
  if (!$param->exists(1) || $param->exists('help', '?')) {
    Console::writeLine(<<<__
Imports a directory of directories of images into a collection of albums
--------------------------------------------------------------------------------
Usage:
  php import.php <<directory>> [<<options>>]

Options:
  --debug, -d     Turns on debugging (default: off)
  --title, -t     Set collection title (default: origin directory name)
  --date, -D      Set collection date (default: origin directory's creation date)
__
    );
    exit(1);
  }

  // Check origin folder
  $origin= new Folder($param->value(1));
  if (!$origin->exists()) {
    Console::writeLinef(
      'The specified folder "%s" does not exist', 
      $origin->getURI()
    );
    exit(2);
  }
  
  // Calculate collection name
  $name= preg_replace('/[^a-z0-9-]/i', '_', $origin->dirname);
  
  // Create destination folder if it doesn't exist yet
  $destination= new Folder(IMAGE_FOLDER.$name);
  try {
    $destination->exists() || $destination->create(0755);
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('===> Starting import at ', date('r'));
  
  // Set up processor
  $processor= new ImageProcessor();
  $processor->fullDimensions= array(800, 600);
  $processor->addFilter(new ConvolveFilter(
    new Kernel('[[-1, -1, -1], [-1, 16, -1], [-1, -1, -1]]'),
    8,
    0
  ));
  
  // Check if debugging output is wanted
  $cat= NULL;
  if ($param->exists('debug')) {
    $cat= Logger::getInstance()->getCategory();
    $cat->addAppender(new ConsoleAppender());
    $processor->setTrace($cat);
  }

  // Check if collection already exists
  $serialized= new File(DATA_FOLDER.$name.'.dat');
  if ($serialized->exists()) {
    Console::writeLine('---> Found existing collection');
    try {
      $collection= unserialize(FileUtil::getContents($serialized));
    } catch (IOException $e) {
      $e->printStackTrace();
      exit(-1);
    }

    $collection->setTitle($param->value('title', 't', (string)$collection->getTitle()));
    $collection->setCreatedAt(new Date($param->value('date', 'D', $collection->createdAt->getTime())));

    // We will regenerate these from scratch...
    $collection->entries= array();
  } else {
    Console::writeLine('---> Creating new collection...');

    // Create collection
    $collection= new EntryCollection();
    $collection->setName($name);
    $collection->setTitle($param->value('title', 't', $origin->dirname));
    $collection->setCreatedAt(new Date($param->value('date', 'D', $origin->createdAt())));
  }
  
  // Create subfolder where album entries will be stored in
  $subfolder= new Folder(DATA_FOLDER.$name);
  try {
    $subfolder->exists() || $subfolder->create(0755);
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }

  // Read the introductory text from description.txt if existant
  if (is_file($df= $origin->getURI().DESCRIPTION_FILE)) {
    $collection->setDescription(file_get_contents($df));
  }

  // Find all albums
  $strategy= new GroupByHourStrategy();
  while ($entry= $origin->getEntry()) {
    $qualified= $origin->getURI().$entry.DIRECTORY_SEPARATOR;
    if (!is_dir($qualified)) continue;
    
    // Create album
    $albumname= preg_replace('/[^a-z0-9-]/i', '_', $entry);
    Console::writeLine('     >> Creating album "', $entry, '" (name= "', $albumname, '")');

    $album= $collection->addEntry(new Album());
    $album->setName($name.'/'.$albumname);

    // Read the title title.txt if existant, use the directory name otherwise
    if (is_file($tf= $qualified.TITLE_FILE)) {
      $album->setTitle(file_get_contents($tf));
    } else {
      $album->setTitle($entry);
    }

    // Read the introductory text from description.txt if existant
    if (is_file($df= $qualified.DESCRIPTION_FILE)) {
      $album->setDescription(file_get_contents($df));
    }

    // Point processor at new destination
    $albumDestination= new Folder($destination->getURI().$albumname);
    try {
      $albumDestination->exists() || $albumDestination->create(0755);
    } catch (IOException $e) {
      $e->printStackTrace();
      exit(-1);
    }
    $processor->setOutputFolder($albumDestination);

    // Check to see if there is a "highlights" folder
    $highlights= new Folder($qualified.HIGHLIGHTS_FOLDER);
    if ($highlights->exists()) {
      for ($i= new FilteredFolderIterator($highlights, FOLDER_FILTER); $i->hasNext(); ) {
        try {
          $highlight= $processor->albumImageFor($i->next());
        } catch (ImagingException $e) {
          $e->printStackTrace();
          exit(-2);
        }

        if (!$highlight->exifData->dateTime) {
          $highlight->exifData->dateTime= $album->getDate();
        }

        if ($iptc= $highlight->getIptcData()) {
          foreach ($iptc->getKeywords() as $keyword) {
            $normalized= strtolower(preg_replace('/[^a-z0-9-]/i', '_', $keyword));
            if (!isset($topics[$normalized])) {
              $topic= new File(DATA_FOLDER.'topics/'.$normalized.'.dat');
              if ($topic->exists()) {
                $topics[$normalized]= unserialize(FileUtil::getContents($topic));
                Console::writeLine('     >> Found existing topic for ', $keyword);
              } else {
                Console::writeLine('     >> Creating new topic for ', $keyword);
                $topics[$normalized]= new Topic();
                $topics[$normalized]->setName($normalized);
                $topics[$normalized]->setTitle($keyword);
                $topics[$normalized]->setCreatedAt($album->getCreatedAt());
              }
            }
            $topics[$normalized]->addImage($highlight, $album->getName());
          }
        }

        $album->addHighlight($highlight);
        Console::writeLine('     >> Added highlight ', $highlight->getName(), ' to album ', $albumname);
      }
      $highlights->close();
    }
    $needsHighlights= HIGHLIGHTS_MAX - $album->numHighlights();
    
    $images= array();
    for ($i= new FilteredFolderIterator(new Folder($qualified), FOLDER_FILTER); $i->hasNext(); ) {
      try {
        $image= $processor->albumImageFor($i->next());
      } catch (ImagingException $e) {
        $e->printStackTrace();
        exit(-2);
      }

      if (!$image->exifData->dateTime) {
        $image->exifData->dateTime= $album->getDate();
      }

      if ($iptc= $image->getIptcData()) {
        foreach ($iptc->getKeywords() as $keyword) {
          $normalized= strtolower(preg_replace('/[^a-z0-9-]/i', '_', $keyword));
          if (!isset($topics[$normalized])) {
            $topic= new File(DATA_FOLDER.'topics/'.$normalized.'.dat');
            if ($topic->exists()) {
              $topics[$normalized]= unserialize(FileUtil::getContents($topic));
              Console::writeLine('     >> Found existing topic for ', $keyword);
            } else {
              Console::writeLine('     >> Creating new topic for ', $keyword);
              $topics[$normalized]= new Topic();
              $topics[$normalized]->setName($normalized);
              $topics[$normalized]->setTitle($keyword);
              $topics[$normalized]->setCreatedAt($album->getCreatedAt());
            }
          }
          $topics[$normalized]->addImage($image, $album->getName());
        }
      }

      $images[]= $image;
      Console::writeLine('     >> Added image ', $image->getName(), ' to album ', $albumname);

      // Check if more highlights are needed
      if ($needsHighlights <= 0) continue;

      Console::writeLine('     >> Need ', $needsHighlights, ' more highlight(s) in ', $albumname, ', using above image');
      $album->addHighlight($image);
      $needsHighlights--;
    }
    
    // Sort images by their creation date (from EXIF data)
    usort($images, create_function(
      '$a, $b', 
      'return $b->exifData->dateTime->compareTo($a->exifData->dateTime);'
    ));

    // Set album creation date from first image in album
    $album->setCreatedAt($images[0]->exifData->dateTime);

    // Divide up into chapters by hour
    $chapter= array();
    for ($i= 0, $s= sizeof($images); $i < $s; $i++) {
      $key= $strategy->groupFor($images[$i]);
      if (!isset($chapter[$key])) {
        $chapter[$key]= $album->addChapter(new AlbumChapter($key));
      }

      $chapter[$key]->addImage($images[$i]);
    }
    
    // Save album and index
    $base= dirname($serialized->getURI()).DIRECTORY_SEPARATOR.$album->getName();
    Console::writeLine('---> Saving album "', $albumname);
    $cat && $cat->debug($album);
    try {
      FileUtil::setContents(new File($base.'.dat'), serialize($album));
      FileUtil::setContents(new File($base.'.idx'), serialize($name));
    } catch (IOException $e) {
      $e->printStackTrace();
      exit(-1);
    }
  }
  $origin->close();

  // Sort entries by their creation date
  usort($collection->entries, create_function(
    '$a, $b', 
    '$date= $b->getDate(); return $date->compareTo($a->getDate());'
  ));


  // Store collection
  Console::writeLine('---> Saving collection to ', $serialized->getURI());
  try {
    FileUtil::setContents($serialized, serialize($collection));
    foreach ($topics as $normalized => $t) {
      FileUtil::setContents(new File(DATA_FOLDER.'topics/'.$normalized.'.dat'), serialize($t));
    }
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Regenerate indexes
  $index= IndexCreator::forFolder(new Folder(DATA_FOLDER));
  $index->setEntriesPerPage(ENTRIES_PER_PAGE);
  $index->setTrace($cat);
  $index->regenerate();

  // Generate topics
  for ($i= new FilteredFolderIterator(new Folder(DATA_FOLDER.'topics'), '/\.dat$/'); $i->hasNext(); ) {
    $entry= $i->next();
    $entries[basename($entry)]= 'topics/'.basename($entry, '.dat');
  }
  ksort($entries);
  try {
    for ($i= 0, $s= sizeof($entries); $i < $s; $i+= ENTRIES_PER_PAGE) {
      FileUtil::setContents(
        new File(DATA_FOLDER.'topics_'.($i / ENTRIES_PER_PAGE).'.idx'), 
        serialize(array(
          'total'   => $s, 
          'perpage' => ENTRIES_PER_PAGE,
          'entries' => array_slice($entries, $i, ENTRIES_PER_PAGE)
        ))
      );
    }
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('===> Finished at ', date('r'));
  // }}}
?>
