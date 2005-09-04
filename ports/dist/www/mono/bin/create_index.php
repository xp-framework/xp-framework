<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.Folder',
    'util.PropertyManager'
  );
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  
  
  $prop= &$pm->getProperties('mono');
  $folder= &new Folder($prop->readString('picture', 'directory'));
  $folder->open();
  
  while ($entry= $folder->getNextEntry()) {
    
  }
  
  /// }}}
?>
