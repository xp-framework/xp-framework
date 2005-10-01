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
    'de.document-root.mono.MonoCatalog'
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
  
  $date= ($p->exists('date') 
    ? $p->value('date') 
    : date('Y/m/d')
  );
  
  $cFile= &new File($datadir.'/dates.idx');
  try(); {
   $cFile->open(FILE_MODE_READWRITE);
   $cFile->lockExclusive();
  } if (catch('IOException', $e)) {
    Console::writeLine('!==> Index file currently locked.');
    exit(-1);
  }
  
  $catalog= unserialize($cFile->read($cFile->size()));

  if (!is('de.document-root.mono.MonoCatalog', $catalog)) {
    Console::writeLine('!==> Index file corrupt.');
    exit(-1);
  }

  // Find out "next" item to publish
  $id= $catalog->getCurrent_id();
  $id && Console::writeLine('---> Current shot is #'.$id.' ('.$catalog->dateFor($id).')');
  
  // Begin with first picture...
  $id= 1;

  // If that one has not been indexed, try to find next
  while (
    ($pFile= &new File($shotdir.'/'.$id.'/picture.idx')) &&
    (!$pFile->exists() || $catalog->hasId($id))
  ) {
    if (!$pFile->exists()) { Console::writeLine('!--> Shot #'.$id.' not yet indexed, skipping.'); }
    if ($catalog->hasId($id)) { Console::writeLine('---> Shot #'.$id.' already published, skipping'); }
    $id++;
  }
  
  if (!$pFile->exists()) {
    Console::writeLine('!--> No new publishable shots found.');
    exit(0);
  }
  
  // Perform check
  try(); {
    $pic= unserialize(FileUtil::getContents(new File($shotdir.'/'.$id.'/picture.idx')));
  } if (catch('IOException', $e)) {
    Console::writeLine('!==> Shot #'.$id.' has not been indexed, yet.');
    exit(-1);
  }
  
  // Checks ok, increase counter and "publish" directory.
  try(); {
    Console::writeLine('===> Adding shot #'.$id.' with date '.$date);
    $catalog->addShot($id, $date);
    $catalog->setCurrent_id($id);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
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
