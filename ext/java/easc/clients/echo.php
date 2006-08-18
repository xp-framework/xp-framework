<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'remote.Remote', 
    'util.cmd.ParamString', 
    'util.profiling.Timer',
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'util.profiling.unittest.AssertionFailedError'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || !$p->exists(2)) {
    Console::writeLine(<<<__
EASC echo bean demo application

Usage
-----
$ php echo.php <hostname> <type>  [-p <port> ] [-j <jndi_name> ] [-v level]
  
  * hostname is the host name (or IP) that your JBoss + XP-MBean server 
    is running on. The feed entity bean (from the easc/beans directory) 
    is expected to be deployed.
  
  * type is one of the following
    - string
    - int
    - double
    - bool
    - null
    - date
    - hash
    - array

  * port is the port the XP-MBean is listening on. It defaults to 6448.
  
  * jndi_name is the name of the bean in JNDI. It defaults to 
    "xp/demo/Echo"
  
  * Level is the log level, defaulting to all, and may consist of one 
    or more of debug, info, warn or error (separated by commas), which
    would selectively activate the specified log level.
__
    );
    exit(1);
  }
  
  $url= 'xp://'.$p->value(1).':'.$p->value('port', 'p', 6448).'/';
  
  // Debugging
  $cat= NULL;
  if ($p->exists('verbose')) {
    $l= &Logger::getInstance();
    $cat= &$l->getCategory();
    $cat->addAppender(new ColoredConsoleAppender());
    $pool= &HandlerInstancePool::getInstance();
    $handler= &$pool->acquire($url);
    $handler->setTrace($cat);
    
    if ($levels= $p->value('verbose')) {
      foreach (explode(',', $levels) as $level) {
        $flags= $flags | constant('LOGGER_FLAG_'.strtoupper($level));
      }
      $cat->setFlags($flags);
    }
  }
  
  try(); {
    $remote= &Remote::forName($url);
    $remote && $home= &$remote->lookup($p->value('jndi', 'j', 'xp/demo/Roundtrip'));
    $home && $instance= &$home->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  switch ($p->value(2)) {
    case 'string':
      $value= 'He was called the "Übercoder", said Tom\'s friend';
      $method= 'echoString';
      break;

    case 'int':
      $value= 1;
      $method= 'echoInt';
      break;

    case 'double':
      $value= 1.5;
      $method= 'echoDouble';
      break;

    case 'bool':
      $value= TRUE;
      $method= 'echoBool';
      break;

    case 'null':
      $value= NULL;
      $method= 'echoNull';
      break;

    case 'date':
      $value= &Date::now();
      $method= 'echoDate';
      break;

    case 'hash':
      $value= array(
        'localpart'   => 'xp',
        'domain'      => 'php3.de'
      );
      $method= 'echoHash';
      break;

    case 'array':
      $value= &new ArrayList(array(1, 2, 3));
      $method= 'echoArray';
      break;

    default:
      throw(new IllegalArgumentException('Unknown type "'.$p->value(2).'"'));
      exit(-2);
  }
  
  Console::writeLine('===> Invoking ', $method, '(', xp::stringOf($value), ')');
  $t= &new Timer(); 
  $t->start(); {
    $return= $instance->$method($value);
  } $t->stop();
  Console::writeLine('---> Return ', xp::stringOf($return));
  Console::writeLinef('---> Took %.3f seconds', $t->elapsedTime());
  
  if (!(is('lang.Object', $value) ? $value->equals($return) : $value === $return)) {
    throw(new AssertionFailedError('Roundtrip failed', $return, $value));
    exit(-3);
  }
  // }}}
?>
