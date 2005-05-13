<?php
/* This file is part of the XP framework website
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.development');
  uses(
    'net.planet-xp.scriptlet.PlanetXPScriptlet',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'util.log.Logger'
  );
  
  // {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure('../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  $cat= &$log->getCategory();

  $cm= &ConnectionManager::getInstance();
  $cm->configure($pm->getProperties('database'));

  scriptlet::run(new PlanetXPScriptlet(
    new ClassLoader('net.planet-xp.scriptlet'), 
    '../xsl/'
  ));
  // }}}  
?>
