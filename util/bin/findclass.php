<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses('io.sys.StdStream', 'util.cmd.ParamString');
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || '-' == $p->value(1)) {
    $stdin= &StdStream::get(STDIN);
    $classname= $stdin->readLine(1024);
  } else {
    $classname= $argv[1];
  }
  
  $cl= &ClassLoader::getDefault();
  if (!($filename= $cl->findClass($classname))) {
    echo 'Class "', $classname, '" not found', "\n";
    exit(-1);
  }
  
  echo $filename, "\n";
  // }}}
?>
