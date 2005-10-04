<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  require('lang.base.php');
  xp::sapi('soap.service');
  uses(
    'util.PropertyManager',
    'util.log.Logger',
    'util.log.FileAppender'
  );
  
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  
  $s= &new SoapRpcRouter(new ClassLoader('net.xp_framework.webservices.interop'));
  try(); {
    $s->init();
    $response= &$s->process();
  } if (catch ('HttpScriptletException', $e)) {
    // Retrieve standard "Internal Server Error"-Document
    $response= &$e->getResponse();
  }
  
  $response->sendHeaders();
  $response->sendContent();
  
  $s->finalize();

?>
