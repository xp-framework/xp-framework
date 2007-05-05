<?php
/* 
 * JSONRouter
 *
 * $Id: index.php 53375 2007-01-30 15:34:16Z clang $
 */
 
  require('lang.base.php');
  xp::sapi('cgi');
  uses(
    'webservices.json.rpc.JsonRpcRouter',
    'util.log.Logger',
    'util.log.FileAppender',
    'util.PropertyManager',
    'rdbms.ConnectionManager'
  );

  // {{{
  
  // Hack: this is required, so we can find the sqlite
  // database file
  chdir(dirname(__FILE__).'/..');
  
  $prop= PropertyManager::getInstance();
  $prop->configure(dirname(__FILE__).'/../../etc/');

  Logger::getInstance()->configure($prop->getProperties('log'));
  ConnectionManager::getInstance()->configure($prop->getProperties('database'));
  
  $s= new JsonRpcRouter('name.kiesel.pxl.service');
  try {
    $s->init();
    $response= $s->process();
  } catch (HttpScriptletException $e) {
    // Retreive standard "Internal Server Error"-Document
    $response= $e->getResponse(); 
  }

  $response->sendHeaders();
  $response->sendContent();

  $s->finalize();
  // }}}
?>
