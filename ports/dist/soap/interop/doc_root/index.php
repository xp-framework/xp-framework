<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.development');
  uses(
    'net.xp_framework.scriptlet.interop.InteropScriptlet',
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

  scriptlet::run(new InteropScriptlet(
    new ClassLoader('net.xp_framework.scriptlet.interop'), 
    '../xsl/'
  ));
?>
