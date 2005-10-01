<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.PropertyManager',
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'de.document-root.mono.MonoCatalog'
  );
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');
  
  $p= &new ParamString();
  $datadir= ($p->exists('datadir')
    ? $p->value('datadir')
    : dirname(__FILE__).'/../data'
  );
  
  // Create data folder if not existing
  $folder= &new Folder($datadir);
  if (!$folder->exists($datadir)) {
    Console::writeLine('---> create data folder "'.$datadir.'"');
    $folder->create(); 
  }
  
  // Create index file if not existing
  $f= &new File($datadir.'/dates.idx');
  if (!$f->exists()) {
    Console::writeLine('---> create index file "'.$f->getURI().'"');
    FileUtil::setContents(
      $f,
      serialize(new MonoCatalog())
    );
  }
  /// }}}
?>
