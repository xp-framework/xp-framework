<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses(
    'util.cmd.ParamString', 
    'io.File',
    'io.FileUtil',
    'util.text.PHPTokenizer'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (2 > $p->count || $p->exists('help', '?')) {
    printf(
      "Usage:   %1\$s <filename> [--debug]\n".
      "Example: find skeleton2/ -name '*.php' -exec php -q -C %1\$s {} \;\n",
      basename($p->value(0))
    );
    exit();
  }
  $DEBUG= $p->exists('debug');
  $filename= realpath($p->value(1));
  
  // Tokenize
  printf("===> %s\n", $filename);
  $t= &new PHPTokenizer();
  try(); {
    $t->setTokenString(FileUtil::getContents(new File($filename)));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  $out= array();
  if ($tok= $t->getFirstToken()) do {
    $DEBUG && printf("%s: %s\n", $t->getTokenName($tok[0]), $tok[1]);
    
    switch (strtolower($tok[1])) {
      case 'function':  // function public, function _private
        while (T_STRING !== $tok[0]) $tok= $t->getNextToken();
        var_dump($tok);
        $out[]= ('_' == $tok[1]{0}) ? 'private' : 'public';
        $out[]= ' function ';
        break;
        
      case 'try':       // try(); {
        while ('{' !== $tok[1]) $tok= $t->getNextToken();
        $out[]= 'try ';
        break;
        
      case 'throw':     // return throw(new Exception('...'));
        $pop= array();
        while ($e= array_pop($out)) { 
          if (0 === strcasecmp('return', $e)) break;
          array_unshift($pop, $e);
          if ('' != trim($e)) {
            $out[]= implode('', $pop);
            break;
          }
        }
        $tok[1].= ' ';
        break;
        
      case 'catch':     // if (catch('Exception', $e)) {
        while (0 != strcasecmp(array_pop($out), 'if')) { }
        $out[]= 'catch (';
        while ('{' !== $tok[1]) {
          switch ($tok[0]) {
            case T_CONSTANT_ENCAPSED_STRING:    // Exception name
              $out[]= substr($tok[1], 1, -1).' ';
              break;
              
            case T_VARIABLE:                    // Variable
              $out[]= $tok[1];
              break;
              
          }
          $tok= $t->getNextToken();
        }
        $out[]= ') ';
        break;
    }
    
    $out[]= $tok[1];
  } while ($tok= $t->getNextToken());
  
  // If we are in debug mode, don't write anything to the file
  if ($DEBUG) {
    echo implode('', $out);
    exit();
  }
  
  $f= &new File($filename);
  try(); {
    $f->open(FILE_MODE_WRITE);
    $f->write(implode('', $out));
    $f->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  // }}}
?>
