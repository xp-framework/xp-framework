<?php
/* This file is part of the XP framework website
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'net.xp-framework.scriptlet.WebsiteScriptlet',
    'util.PropertyManager',
    'util.log.Logger'
  );
  
  // {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure('../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  $cat= &$log->getCategory();

  $scriptlet= &new WebsiteScriptlet(
    new ClassLoader('net.xp-framework.scriptlet'), 
    '../xsl/'
  );
  try(); {
    $scriptlet->init();
    $response= &$scriptlet->process();
  } if (catch('HttpScriptletException', $e)) {
    $response= &$e->getResponse();
  
    // Retrieve ErrorDocument
    $response->setContent(str_replace(
      '<xp:value-of select="reason"/>',
      $e->toString(),
      file_get_contents('error'.$response->statusCode.'.html')
    ));
  }
  
  // Send output
  $response->sendHeaders();
  $response->sendContent();
  flush();
  
  $scriptlet->finalize();
  // }}}  
?>
