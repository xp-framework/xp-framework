<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli', 'soap.client');
  uses(
    'util.PropertyManager',
    'util.log.Logger'
  );
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  
  $param= &new ParamString();
  if ($param->count < 3) {
    Console::writeLinef('%s: <url> <uri>', $param->value(0));
    exit(1);
  }
  
  try(); {
    Console::writeLine('===> Creating SOAP client');
    $client= &new SoapClient(
      new SoapHttpTransport($param->value(1)),
      $param->value(2)
    );
    
    $client->setTrace($log->getCategory());
    
  } if (catch ('SOAPFaultException', $e)) {
    $e->printStackTrace();
  }
  
  $round2methods= array(
    'echoString'  => array(
      'args'    => array('Hello Worlds'),
      'result'  => 'Hello World'
    ),
    'echoInteger' => array(
      'args'    => array(42),
      'result'  => 42
    )
  );
  
  foreach (array_keys($round2methods) as $fn) {
    Console::writeLinef('===> Preparing to call Base 2 method "%s"', $fn);
    
    try(); {
      $args= array_merge(array($fn), $round2methods[$fn]['args']);
      $result= &call_user_func_array(
        array(&$client, 'invoke'),
        $args
      );
      
      Console::writeLinef('     Result: %s',
        $result === $round2methods[$fn]['result'] ? 'PASSED' : 'FAILED'
      );
    } if (catch ('SoapFaultException', $e)) {
      $e->printStackTrace();
    }
    
    Console::writeLine();
  }
  

?>
