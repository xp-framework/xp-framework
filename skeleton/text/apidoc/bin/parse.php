<?php
/* This file is part of the XP framework
 * 
 * @purpose Test classparser
 * 
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'lang.apidoc.parser.ClassParser', 
    'util.cmd.ParamString',
    'util.log.Logger',
    'util.log.FileAppender'
  );
  
  $param= &new ParamString();
  if (!$param->exists(1)) {
    printf("Usage: %s <filename>\n", basename($param->value(0)));
    exit();
  }

  // Set up a LogCategory
  $log= &Logger::getInstance();
  $cat= &$log->getCategory();
  $cat->addAppender(new FileAppender('php://stderr'));
  
  // Create a parser
  $parser= &new ClassParser();
  
  try(); {
    $parser->setFile(new File($param->value(1)));
    $result= &$parser->parse($cat);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    continue;
  }

  var_export($result);
?>
