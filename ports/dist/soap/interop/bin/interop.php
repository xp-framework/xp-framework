<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli', 'soap.client');
  uses(
    'util.PropertyManager',
    'util.log.Logger',
    'util.log.BufferedAppender',
    'io.File',
    'io.FileUtil',
    'io.Folder',
    'xml.soap.interop.Round2BaseClient'
  );

  /// {{{ int checkService(string section)
  function checkService($service) {
    $pm= &PropertyManager::getInstance();
    $prop= &$pm->getProperties('services');
    
    // Retrieve logger for the tests
    $log= &Logger::getInstance();
    $scat= &$log->getCategory('soap');
    $appender= &new BufferedAppender();
    $scat->addAppender($appender);
    
    $acat= &$log->getCategory('end-to-end');
    $eeappend= &new BufferedAppender();
    $acat->addAppender($eeappend);
    
    Console::writeLine('===> Testing service ', $service);
    
    $client= &new Round2BaseClient(
      new SOAPHTTPTransport($prop->readString($service, 'uri'), array()),
      $prop->readString($service, 'urn')
    );
    
    $client->setTrace($scat);
    $client->setInputOutputTrace($acat);
    
    // Get all available test methods
    $class= &$client->getClass();
    $methods= &$class->getMethods();
    
    foreach (array_keys($methods) as $m) {
      $method= &$methods[$m];
      
      // Don't call non-public methods
      if (MODIFIER_PUBLIC != $method->getModifiers())
        continue;
      
      // Don't call inherited methods
      if (!$class->equals($method->getDeclaringClass()))
        continue;
      
      // Only call echo*-methods
      if ('echo' != substr($method->getName(), 0, 4))
        continue;
      
      Console::writeLinef('---> Calling Round2 Base method "%s"', $method->getName());

      // Prepare tracking directory
      try(); {
        $folder= &new Folder(dirname(__FILE__).'/../log/'.$service);
        if (!$folder->exists()) {
          Console::writeLine('Creating folder ', $folder->getUri());
          $folder->create(0755);
        }
      } if (catch ('IOException', $e)) {
        Console::writeLine('Could not create tracking folder ', $folder->getUri());
        return FALSE;
      }
      
      try(); {
        $result= &call_user_func_array(array(&$client, $method->getName()), 'dummy');
        Console::writeLinef('     Result: %s',
          $result === TRUE ? 'PASSED' : 'FAILED'
        );

      } if (catch ('SoapFaultException', $e)) {
        $f= &$e->getFault();
        
        Console::writeLinef('SoapFault: [%d] %s', $f->getFaultcode(), $f->getFaultString());
        FileUtil::setContents(
          new File(sprintf('%s/%s.errmsg', $folder->getUri(), $method->getName())),
          sprintf('SoapFault: [%d] %s', $f->getFaultcode(), $f->getFaultString())
        );
        
        FileUtil::setContents(
          new File(sprintf('%s/%s.stacktrace', $folder->getUri(), $method->getName())),
          $e->toString()
        );
        
      } if (catch ('Exception', $e)) {
        FileUtil::setContents(
          new File(sprintf('%s/%s.stacktrace', $folder->getUri(), $method->getName())),
          $e->toString()
        );
      }
      
      // If nonempty, write the session logs into file.
      if (strlen($b= $appender->getBuffer())) {
        FileUtil::setContents(
          new File(sprintf('%s/%s.log', $folder->getUri(), $method->getName())),
          $b
        );
      }
      
      // If nonempty, write the session logs into file.
      if (strlen($b= $eeappend->getBuffer())) {
        FileUtil::setContents(
          new File(sprintf('%s/%s.inout', $folder->getUri(), $method->getName())),
          $b
        );
      }
      
      $appender->clear();
      $eeappend->clear();
    }
    
    Console::writeLinef('===> Testing for service %s has ended.', $service);
  }
  /// }}}
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  
  $param= &new ParamString();
  if ($param->exists('help', 'h')) {
    Console::writeLinef('%s [--service=NAME] [--list]', $param->value(0));
    exit(1);
  }

  // Open property file
  $prop= &$pm->getProperties('services');
  
  if ($param->exists('list', 'l')) {
    Console::writeLine('List of available servies:');
    $section= $prop->getFirstSection();
    
    do {
      Console::writeLinef('  * Service: %s', $section);
      Console::writeLinef('    uri= %s', $prop->readString($section, 'uri'));
      Console::writeLinef('    urn= %s', $prop->readString($section, 'urn'));
      Console::writeLine();
      
    } while ($section= $prop->getNextSection());
    
    exit(0);
  }
  
  // If service specified, only check that one...
  if ($param->exists('service')) {
    if (!$prop->hasSection($param->value('service'))) {
      Console::writeLinef('Service does not exists: %s', $param->value('service'));
      exit(1);
    }
    
    $r= checkService($param->value('service'));
    exit($r ? 0 : 1);
  }
  
  // Default mode: iterate through all configured services
  $section= $prop->getFirstSection();

  do {
    
    checkService($section);
  } while ($section= $prop->getNextSection());
    
?>
