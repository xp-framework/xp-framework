<?php
/* This file is part of the XP framework website
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production');
  uses(
    'net.xp_framework.scriptlet.WebsiteScriptlet',
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

  scriptlet::run(new WebsiteScriptlet(
    'net.xp_framework.scriptlet', 
    '../xsl/'
  ));
  // }}}  
?>
