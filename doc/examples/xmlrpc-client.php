<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli', 'xmlrpc.client');
  uses('util.log.Logger', 'util.log.LogCategory', 'util.log.ColoredConsoleAppender');
  
  /// {{{ main
  $log= &Logger::getInstance();
  $cat= &$log->getCategory();
  $cat->addAppender(new ColoredConsoleAppender());
  
  $c= &new XmlRpcClient(new XmlRpcHttpTransport('http://xmlrpc.boost.home.ahk:80'));
  $c->setTrace($cat);
  
  try(); {
    $res= &$c->invoke('XmlRpcTest::echoArguments', 3, 'foobar', TRUE, -5.666667, array('foo' => 'bar'));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine(xp::stringOf($res));
  // }}}
?>
