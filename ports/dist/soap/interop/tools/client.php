<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  require('lang.base.php');
  xp::sapi('cli', 'soap.client');
  uses(
    'util.log.Logger',
    'util.log.FileAppender'
  );
  
  // Configure logger
  $log= &Logger::getInstance();
  $cat= &$log->getCategory();
  $cat->addAppender(new FileAppender('php://stdout'));
  
  // Invoke SOAP call
  try(); {
    $client= &new SoapClient(
      new SoapHttpTransport('http://interop.xp-framework.net'),
      'Round2'
    );
    
    $response= $client->invoke('echoString', 'Hello World');
  } if (catch ('SOAPFaultException', $e)) {
    $e->printStackTrace();
    exit(1);
  }
  
  Console::writeLine('Reponse was: '. var_export($response, 1));
?>
  
