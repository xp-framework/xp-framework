<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.streams.PrintStream', 
    'io.streams.ConsoleOutputStream',
    'util.log.Logger',
    'util.PropertyManager',
    'rdbms.ConnectionManager'
  );

  // {{{ main
  $params= new ParamString();
  
  // Separate runner options from class options
  $map= array();
  $options= array(
    'config'  => 'etc'
  );
  $valid= array(
    'config'  => 1,
  );
  foreach ($valid as $key => $val) {
    $valid[$key{0}]= $val;
    $map[$key{0}]= $key;
  }
  $classname= NULL;
  for ($i= 1; $i < $params->count; $i++) {
    $option= $params->list[$i];

    if (0 == strncmp($option, '--', 2)) {        // Long: --foo / --foo=bar
      $p= strpos($option, '=');
      $name= substr($option, 2, FALSE === $p ? strlen($option) : $p- 2);
      if (isset($valid[$name])) {
        if ($valid[$name] == 1) {
          $options[$name]= FALSE === $p ? NULL : substr($option, $p+ 1);
        } else {
          $options[$name]= TRUE;
        }
      }
    } else if (0 == strncmp($option, '-', 1)) {   // Short: -f / -f bar
      $short= substr($option, 1);
      if (isset($valid[$short])) {
        if ($valid[$short] == 1) {
          $options[$map[$short]]= $params->list[++$i];
        } else {
          $options[$map[$short]]= TRUE;
        }
      }
    } else {
      unset($params->list[-1]);
      $classname= $option;
      $classparams= new ParamString(array_slice($params->list, $i+ 1));
      break;
    }
  }
  
  // Sanity check
  if (!$classname) {
    Console::writeLine('*** Missing classname');
    exit(1);
  }
  try {
    $class= XPClass::forName($classname);
  } catch (ClassNotFoundException $e) {
    Console::writeLine('*** ', $e->getMessage());
    exit(1);
  }  
  
  // Usage
  if ($classparams->exists('help', '?')) {
    foreach ($class->getMethods() as $method) {
      if (!$method->hasAnnotation('arg')) continue;
      
      $arg= $method->getAnnotation('arg');
      $name= strtolower(preg_replace('/^set/', '', $method->getName()));;
      $comment= trim($method->getComment());
      
      if (0 == $method->numArguments()) {
        $optional= TRUE;
      } else {
        list($first, )= $method->getArguments();
        $optional= $first->isOptional();
      }
      
      if (isset($arg['position'])) {
        $details['#'.($arg['position'] + 1)]= $comment;
        $positional[$arg['position']]= $name;
      } else if (isset($arg['name'])) {
        $details['--'.$arg['name'].' | -'.(isset($arg['short']) ? $arg['short'] : $arg['name']{0})]= $comment;
        $extra[$arg['name']]= $optional;
      } else {
        $details['--'.$name.' | -'.(isset($arg['short']) ? $arg['short'] : $name{0})]= $comment;
        $extra[$name]= $optional;
      }
    }
    
    // Usage
    asort($positional);
    Console::write('Usage: $ xpcli ', $class->getName(), ' ');
    foreach ($positional as $name) {
      Console::write('<', $name, '> ');
    }
    foreach ($extra as $name => $optional) {
      Console::write(($optional ? '[' : ''), '--', $name, ($optional ? '] ' : ' '));
    }
    Console::writeLine();
    
    // Argument details
    Console::writeLine('Arguments:');
    foreach ($details as $which => $comment) {
      Console::writeLine('* ', $which, "\n  ", $comment, "\n");
    }
    exit(1);
  }

  // Load, instantiate and initialize
  $pm= PropertyManager::getInstance();
  $pm->configure($options['config']);
  
  $cm= ConnectionManager::getInstance();
  $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));

  $l= Logger::getInstance();
  $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));
  
  $instance= $class->newInstance();
  $instance->out= new PrintStream(new ConsoleOutputStream(STDOUT));
  $instance->err= new PrintStream(new ConsoleOutputStream(STDERR));
  
  foreach ($class->getMethods() as $method) {
    if ($method->hasAnnotation('inject')) {     // Perform injection
      $inject= $method->getAnnotation('inject');
      switch ($inject['type']) {
        case 'rdbms.DBConnection': {
          $args= array($cm->getByHost($inject['name'], 0));
          break;
        }
        
        case 'util.Properties': {
          $args= array($pm->getProperties($inject['name']));
          break;
        }
        
        case 'util.log.LogCategory': {
          $args= array($l->getCategory($inject['name']));
          break;
        }

        default: {
          Console::writeLine('*** Unknown injection type "'.$inject['type'].'"');
          exit(-1);
        }
      }
      
      try {
        $method->invoke($instance, $args);
      } catch (Throwable $e) {
        Console::writeLine('*** Error injecting '.$inject['name'].': '.$e->getMessage());
        exit(-2);
      }
    } else if ($method->hasAnnotation('arg')) { // Pass arguments
      $arg= $method->getAnnotation('arg');
      if (isset($arg['position'])) {
        $name= '#'.$arg['position'];
        $select= intval($arg['position']);
        $short= NULL;
      } else if (isset($arg['name'])) {
        $name= $select= $arg['name'];
        $short= isset($arg['short']) ? $arg['short'] : NULL;
      } else {
        $name= $select= strtolower(preg_replace('/^set/', '', $method->getName()));
        $short= isset($arg['short']) ? $arg['short'] : NULL;
      }
      
      if (0 == $method->numArguments()) {
        if (!$classparams->exists($select, $short)) continue;
        $args= array();
      } else if (!$classparams->exists($select, $short)) {
        list($first, )= $method->getArguments();
        if (!$first->isOptional()) {
          Console::writeLine('*** Argument '.$name.' does not exist!');
          exit(-1);
        }

        $args= array();
      } else {
        $args= array($classparams->value($select, $short));
      }

      try {
        $method->invoke($instance, $args);
      } catch (Throwable $e) {
        Console::writeLine('*** Error for argument '.$name.': '.$e->getMessage());
        exit(-2);
      }
    }
  }
  
  $instance->run();
  // }}}
?>
