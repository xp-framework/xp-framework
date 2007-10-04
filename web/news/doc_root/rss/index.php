<?php
  require('lang.base.php');
  xp::sapi('cgi', 'scriptlet.development');
  uses('net.xp_framework.website.news.scriptlet.RssFeedScriptlet',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'util.log.Logger'
  );
  
  // {{{ main
  with ($pm= PropertyManager::getInstance()); {
  	$pm->configure('../../etc/');
  	Logger::getInstance()->configure($pm->getProperties('log'));
  	ConnectionManager::getInstance()->configure($pm->getProperties('database'));
  }

  scriptlet::run(new RssFeedScriptlet());

?>