<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
 
  require('lang.base.php');
  // xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.PropertyManager',
    'de.document-root.gui.gtk.project.ProjectManager',
    'util.cmd.ParamString'
   );

  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');

  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));

  $param= &new ParamString();

  $app= &new ProjectManager($param, dirname(__FILE__).'/../');
  $app->init();
  $app->run();
  $app->done();
 
 /// }}} 
?>
