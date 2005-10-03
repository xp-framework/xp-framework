<?php
/* This file is part of the XP framework ports
 *
 * $Id: index.php 5717 2005-09-04 09:18:28Z kiesel $ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.development');
  uses(
    'util.PropertyManager',
    'util.log.Logger',
    'de.document-root.mono.scriptlet.MonoScriptlet'
  );
  
  // {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure('../../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  $cat= &$log->getCategory();

  // $cm= &ConnectionManager::getInstance();
  // $cm->configure($pm->getProperties('database'));

  // Hardcode product and state
  putenv('PRODUCT=mono');
  putenv('LANGUAGE=en_US');
  putenv('STATE=rss2');

  scriptlet::run(new MonoScriptlet(
    new ClassLoader('de.document-root.mono.scriptlet'), 
    '../../xsl/'
  ));
  // }}}  
?>
