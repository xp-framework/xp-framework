<?php
/* This file is part of the XP framework website
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production', 'cgi');
  uses(
    'net.xp_framework.website.forge.scriptlet.ForgeScriptlet',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'util.log.Logger'
  );
  
  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure('../etc/');
  
  Logger::getInstance()->configure($pm->getProperties('log'));
  ConnectionManager::getInstance()->configure($pm->getProperties('database'));

  scriptlet::run(new ForgeScriptlet(
    'net.xp_framework.website.forge.scriptlet', 
    '../xsl/'
  ));
  // }}}  
?>
