<?php
/* This file is part of the XP framework's peoples' experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.cca.Archive', 'io.File');

  // {{{ main
  $out= &new File('collections.cca');
  $a= &new Archive($out);
  $cl= &ClassLoader::getDefault();
  try(); {
    $a->open(ARCHIVE_CREATE);
    foreach (array(
      'util.collections.DJBX33AHashImplementation',
      'util.collections.HashImplementation',
      'util.collections.HashProvider',
      'util.collections.HashSet',
      'util.collections.HashTable',
      'util.collections.LRUBuffer',
      'util.collections.Map',
      'util.collections.Queue',
      'util.collections.Set',
      'util.collections.Stack',
    ) as $classname) {
      Console::writeLinef('---> Adding %s', $classname);
      $a->add(new File($cl->findClass($classname)), $classname);
    }
    $a->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLinef('===> Wrote %s', $out->getURI());
  // }}}
?>
