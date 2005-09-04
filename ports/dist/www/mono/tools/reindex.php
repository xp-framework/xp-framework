<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.FileUtil',
    'util.PropertyManager',
    'de.document-root.mono.MonoCatalog',
    'de.document-root.mono.MonoPictureScanner'
  );
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');
  
  $p= &new ParamString();
  
  $shotdir= ($p->exists('shotdir')
    ? $p->value('shotdir')
    : dirname(__FILE__).'/../doc_root/shots'
  );
  
  $datadir= ($p->exists('datadir')
    ? $p->value('datadir')
    : dirname(__FILE__).'/../data'
  );
  
  $catalog= unserialize(FileUtil::getContents(new File($datadir.'/general.idx')));
  
  $f= &new Folder($shotdir);
  
  while ($entry= $f->getEntry()) {
    if (
      '.' == $entry{0} ||
      !is_numeric($entry) ||
      !is_dir($f->getURI().'/'.$entry)
    ) continue;
    
    if ($catalog->containsId($entry))
      continue;
    
    // Add picture to index
    try(); {
      $scanner= &new MonoPictureScanner();
      $scanner->setPath($f->getURI().'/'.$entry);
      $pic= &$scanner->create();
    } if (catch('Exception', $e)) {
      $e->printStackTrace();
      exit(-1);
    }
    
    if (!is('de.document-root.mono.MonoPicture', $pic)) {
      Console::writeLine('!--> Got no picture from '.$entry.', continuing');
      continue;
    }
    
    $catalog->appendShot($entry);
    
    // Store MonoPicture in the current directory
    FileUtil::setContents(
      new File($f->getURI().'/'.$entry.'/picture.idx'),
      serialize($pic)
    );
    
    // Store catalog
    FileUtil::setContents(
      new File($datadir.'/general.idx'),
      serialize($catalog)
    );
    
    Console::writeLine('===> Picture index for directory '.$entry.' created.');
  }
  /// }}}
?>
