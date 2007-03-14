<?php
/* This file is part of the XP framework ports
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.development', 'cgi');
  uses(
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'util.log.Logger',
    'de.uska.scriptlet.UskaScriptlet'
  );
  
  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure('../etc/');
  
  Logger::getInstance()->configure($pm->getProperties('log'));
  ConnectionManager::getInstance()->configure($pm->getProperties('database'));

  scriptlet::run(new UskaScriptlet('de.uska.scriptlet', '../xsl/'));
  // }}}  
?>
