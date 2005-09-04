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
  
  $cFile= &new File($datadir.'/general.idx');
  try(); {
   $cFile->open(FILE_MODE_READWRITE);
   $cFile->lockExclusive();
  } if (catch('IOException', $e)) {
    Console::writeLine('!==> Index file currently locked.');
    exit(-1);
  }
  
  $catalog= unserialize($cFile->read($cFile->size()));

  // Find out "next" item to publish
  $id= $catalog->getLast_id() + 1;

  // If that one has already been published, try to find next
  // Is that a good idea not to rely on our index?
  while (
    $pFile= &new File($shotdir.'/'.$id.'/published') &&
    $pFile->exists()
  ) {
    Console::writeLine('!--> Picture '.$id.' already published, fetching next.');
    $id++;
  }
  
  // Perform check
  try(); {
    $pic= unserialize(FileUtil::getContents(new File($shotdir.'/'.$id.'/picture.idx')));
  } if (catch('IOException', $e)) {
    Console::writeLine('!==> Picture '.$id.' has not been indexed, yet.');
    exit(-1);
  }
  
  // Checks ok, increase counter and "publish" directory.
  $catalog->setLast_id($id);
  $pFile->touch();
  
  try(); {
    $cFile->truncate();
    $cFile->rewind();
    $cFile->write(serialize($catalog));
    $cFile->unLock();
    $cFile->close();
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
  }
  
  Console::writeLine('===> Picture '.$id.' has been published.');
?>
