<?php
/* This file is part of the XP framework website
 *
 * $Id: index.php 8216 2006-10-22 10:13:38Z kiesel $ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production', 'cgi');
  uses(
    'net.xp_framework.website.news.scriptlet.NewsScriptlet',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'util.log.Logger'
  );
  
  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure('../etc/');
  
  Logger::getInstance()->configure($pm->getProperties('log'));
  ConnectionManager::getInstance()->configure($pm->getProperties('database'));

  scriptlet::run(new NewsScriptlet(
    'net.xp_framework.website.news.scriptlet', 
    '../xsl/'
  ));
  // }}}  
?>
