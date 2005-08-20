<?php
/* This file is part of the XP framework ports
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.development');
  uses(
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'util.log.Logger',
    'de.uska.scriptlet.UskaScriptlet'
  );
  
  // {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure('../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  $cat= &$log->getCategory();

  $cm= &ConnectionManager::getInstance();
  $cm->configure($pm->getProperties('database'));

  scriptlet::run(new UskaScriptlet(
    new ClassLoader('de.uska.scriptlet'), 
    '../xsl/'
  ));
  // }}}  
?>
