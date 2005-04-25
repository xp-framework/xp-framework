<?php
  /* This file is part of the XP framework's experiments
   *
   * $Id$
   */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.FileUtil'
  );
  
  function fetchuses() {
    $args= func_get_args();
    return $args;
  }

  /// {{{ main
  $param= &new ParamString();
  $filename= $param->value(1);
  $source= FileUtil::getContents(new File($filename));
  
  // Find name of function to overwrite
  $str= file_get_contents('php://stdin');
  if (!preg_match('/(function\w+)?(\w+)/', $str, $match)) {
    Console::writeLine('Do not know which function to overwrite...');
    exit(-1);
  }
  $function= $match[2];
  
  // Find the class this class extends from
  if (!preg_match('/class \w+ extends (\w+) {/im', $source, $match)) {
    Console::writeLine('Could not find class declaration...');
    exit(-1);
  }
  $extends= $match[1];
  
  // Check for builtin classes
  $fqcn= NULL;
  if (class_exists(xp::reflect($extends))) {
    $fqcn= xp::nameOf(xp::reflect($extends));
  } else {
    
    // Find the uses()-list
    if (!preg_match('/^  uses\(([^\)]+)\);/mi', $source, $match)) {
      Console::writeLine('Could not find uses() list...');
      exit(-1);
    }
    $usesstr= $match[1];

    // Hack to find included classes
    eval('$classes= fetchuses('.$usesstr.');');
  
    foreach ($classes as $class) {
      if (strcasecmp(substr($class, -strlen($extends)), $extends) == 0) {
        $fqcn= $class;
        break;
      }
    }
  }
  
  if (!$fqcn) {
    Console::writeLine('Could not find parent class.');
    exit(-1);
  }
  
  // Load parent class
  try(); {
    $class= &XPClass::forName($fqcn);
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Special case: __construct()
  if ('__construct' == $function) {
    $method= &$class->getConstructor();
    var_dump($method);
  } else {
    $method= &$class->getMethod($function);
  }

  if (!$method) {
    Console::writeLine('No such method: '.$function);
    exit(-1);
  }

  
  // Now output function declaration
  Console::writeLinef('    /**');
  if (NULL !== $method->getComment()) {
    foreach (explode("\n", trim($method->getComment())) as $line) {
      Console::writeLinef('     * %s', $line);
    }
  }
  Console::writeLinef('     *');
  
  // Model
  if ($mask= $method->getModifiers() & (MODIFIER_STATIC|MODIFIER_ABSTRACT|MODIFIER_FINAL)) {
    $names= array(
      MODIFIER_ABSTRACT     => 'abstract',
      MODIFIER_STATIC       => 'static',
      MODIFIER_FINAL        => 'final'
    );
    Console::writeLinef('     * @model   %s', $names[$mask]);
  }
  
  // Access
  if ($mask= $method->getModifiers() & (MODIFIER_PUBLIC|MODIFIER_PROTECTED|MODIFIER_PRIVATE)) {
    $names= array(
      MODIFIER_PUBLIC       => 'public',
      MODIFIER_PROTECTED    => 'protected',
      MODIFIER_PRIVATE      => 'private'
    );
    Console::writeLinef('     * @access  %s', $names[$mask]);
  }
  
  // Parameters
  $args= &$method->getArguments();
  foreach (array_keys($args) as $idx) {
    Console::writeLinef('     * @param   %s %s%s',
      $args[$idx]->getType(),
      $args[$idx]->getName(),
      ($args[$idx]->isOptional() ? '= '.$args[$idx]->getDefault() : '')
    );
  }
  
  // Return type
  if ($ret= $method->getReturnType()) {
    Console::writeLinef('     * @return  %s', $ret);
  }
  
  Console::writeLine('     */');
  
  // Function declaration
  Console::writef('    function %s(', $method->getName(TRUE));
  
  // Parameters
  foreach (array_keys($args) as $idx) {
    Console::writef('%s$%s',
      (strncmp('&', $args[$idx]->getType(), 1) == 0 ? '&' : ''),
      $args[$idx]->getName()
    );
    if ($args[$idx]->isOptional())
      Console::write('= '.$args[$idx]->getDefault());
    
    if (sizeof($args)-1 > $idx) Console::write(', ');
  }
  Console::writeLine(') {');
  Console::writeLine('      // TODO: Write code');
  Console::writeLine('    }');
    
  // }}}
?>
