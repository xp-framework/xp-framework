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
    'de.thekid.dialog.SingleShot',
    'de.thekid.dialog.Topic',
    'de.thekid.dialog.io.ShotProcessor',
    'de.thekid.dialog.io.IndexCreator',
    'img.filter.ConvolveFilter'
  );

  define('ENTRIES_PER_PAGE',  5);
  define('DESCRIPTION_EXT',  '.txt');
  
  define('DATA_FOLDER',       dirname(__FILE__).'/../data/');
  define('IMAGE_FOLDER',      dirname(__FILE__).'/../doc_root/shots/');
  
  // {{{ main
  $param= new ParamString();
  if (!$param->exists(1) || $param->exists('help', '?')) {
    Console::writeLine(<<<__
Imports a directory of images into an album
--------------------------------------------------------------------------------
Usage:
  php add_singleshot.php <<image_file>> [<<options>>]

Options:
  --debug, -d     Turns on debugging (default: off)
  --title, -t     Set shot title (default: origin file name)
  --desc, -E      Set description in case description file does not exist
  --date, -D      Set album date (default: origin file's creation date)
__
    );
    exit(1);
  }

  // Check origin file
  $origin= new File($param->value(1));
  if (!$origin->exists()) {
    Console::writeLinef(
      'The specified file "%s" does not exist', 
      $origin->getURI()
    );
    exit(2);
  }

  // Calculate shot name
  $filename= substr($origin->getFilename(), 0, strpos($origin->getFilename(), '.'));
  $name= preg_replace('/[^a-z0-9-]/i', '_', $filename);
  
  // Create destination folder if it doesn't exist yet
  $destination= new Folder(IMAGE_FOLDER);
  try {
    $destination->exists() || $destination->create(0755);
  } catch (IOException $e) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine('===> Starting import at ', date('r'));

  // Set up processor
  $processor= new ShotProcessor();
  $processor->detailDimensions= array(619, 347);
  $processor->fullDimensions= array(800, 600);
  $processor->addFilter(new ConvolveFilter(
    new Kernel('[[-1, -1, -1], [-1, 16, -1], [-1, -1, -1]]'),
    8,
    0
  ));
  $processor->setOutputFolder($destination);
  
  // Check if debugging output is wanted
  $cat= NULL;
  if ($param->exists('debug')) {
    $cat= Logger::getInstance()->getCategory();
    $cat->addAppender(new ConsoleAppender());
    $processor->setTrace($cat);
  }
  
  with ($shot= new SingleShot()); {
    $shot->setName($name);
    $shot->setFileName($origin->getFilename());
    $shot->setTitle($param->value('title', 't', $origin->filename));
    $shot->setDate(new Date($param->value('date', 'D', $origin->createdAt())));

    // Read the introductory text from [filename].txt if existant
    if (is_file($df= $origin->getPath().DIRECTORY_SEPARATOR.$filename.DESCRIPTION_EXT)) {
      Console::writeLine('---> Using description from ', $df);
      $shot->setDescription(file_get_contents($df));
    } else {
      $shot->setDescription($param->value('desc', 'E', ''));
    }

    try {
      $image= $processor->albumImageFor($origin->getURI());
    } catch (ImagingException $e) {
      $e->printStackTrace();
      exit(-2);
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
            $topics[$normalized]->setCreatedAt($shot->getDate());
          }
        }
        $topics[$normalized]->addImage($image, $shot->getName());
      }
    }

    if (!$image->exifData->dateTime) {
      $image->exifData->dateTime= $shot->getDate();
    }
    
    $shot->setImage($image);
  }

  // Save shot
  $serialized= new File(DATA_FOLDER.$name.'.dat');
  $cat && $cat->debug($shot);
  try {
    FileUtil::setContents($serialized, serialize($shot));
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
