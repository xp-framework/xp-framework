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
    'text.PHPTokenizer'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (2 > $p->count || $p->exists('help', '?')) {
    printf(
      "Usage:   %1\$s <filename> [--debug]\n".
      "Example: find skeleton/ -name '*.php' -exec php -C %1\$s {} \;\n",
      basename($p->value(0))
    );
    exit();
  }
  $DEBUG= $p->exists('debug');
  $filename= realpath($p->value(1));

  // Calculate namespace
  preg_match('#skeleton/([a-z/]+)/([^\.]+)\.class\.php$#', $filename, $matches);
  $namespace= strtr($matches[1], '/', ':');
    
  // Tokenize
  $t= &new PHPTokenizer();
  try(); {
    $t->setTokenString(FileUtil::getContents(new File($filename)));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  $out= array();
  if (!($tok= $t->getFirstToken()) || T_OPEN_TAG !== $tok[0]) {
    printf(
      "Expecting T_OPEN_TAG, have %s [%s]\n",
      $t->getTokenName($tok[0]), 
      $tok[1]
    );
    exit(-1);
  }
  
  $header= "<?php\n";
  $tok= $t->getNextToken();
  if (T_COMMENT !== $tok[0]) {
    $header.= <<<__
/* This class is part of the XP framework
 *
 * \$Id\$ 
 */

__;
  } else {
    $header.= $tok[1]."\n\n";
    $tok= array(T_NONE, '');
  }
  
  // Swallow leading whitespace
  while (T_WHITESPACE === $tok[0]) $tok= $t->getNextToken();
  
  $class= FALSE;
  $uses= $constants= array();
  do {
    $DEBUG && printf("%s: %s\n", $t->getTokenName($tok[0]), $tok[1]);
    if (T_COMMENT === $tok[0] && '//' == substr($tok[1], 0, 2) && !$class) continue;
    if (T_CLOSE_TAG === $tok[0]) break;
    
    switch (strtolower($tok[1])) {
      case 'uses':
        while (')' !== $tok[1]) {
          if (T_CONSTANT_ENCAPSED_STRING === $tok[0]) $uses[]= $tok[1];
          $tok= $t->getNextToken();
        }
        $tok= $t->getNextToken(); // Swallow ";"
        $tok= array(T_NONE, '');
        break;
        
      case 'define':
        while (T_CONSTANT_ENCAPSED_STRING !== $tok[0]) $tok= $t->getNextToken();
        $name= $tok[1];
        do {
          $tok= $t->getNextToken();
        } while (T_WHITESPACE === $tok[0] || ',' == $tok[1]);
        $constants[$name]= $tok;
        while (';' !== $tok[1]) $tok= $t->getNextToken();
        $tok= array(T_NONE, '');
        break;
      
      case 'parent':        // Object no longer has constructors
        $parentcall= '';
        $remove= FALSE;
        while (';' !== $tok[1]) {
          $parentcall.= $tok[1];
          $tok= $t->getNextToken();
          if (
            ('__construct' == $tok[1] || '__destruct' == $tok[1]) && 
            ('Object' == $extends)
          ) {
            $remove= TRUE;
          }
        }
        $out[]= $remove ? '' : $parentcall;
        $tok= array(T_NONE, '');
        break;
      
      case 'class':        // class StdStream extends Object {
        $map= array(
          'Object'      => 'lang::Object',
          'Exception'   => 'lang::Exception'
        );
        $out[]= 'class';
        while ('{' !== $tok[1]) {
          $tok= $t->getNextToken();
          $out[]= $tok[1];
          
          if ('extends' == $tok[1]) {
            $tok= $t->getNextToken();   // Swallow whitespace
            $out[]= $tok[1];
            $tok= $t->getNextToken();
            $extends= $tok[1];
            $out[]= isset($map[$tok[1]]) ? $map[$tok[1]] : $namespace.'::'.$tok[1];
          }
        }
        $out[]= "\n";
        foreach ($constants as $name => $tok) {
          $out[]= '    const '.substr($name, 1, -1).' = '.$tok[1].";\n";
        }
        $class= TRUE;
        $tok= array(T_NONE, '');
        break;
        
      case 'var':
        $tok[1]= 'public';
        break;
        
      case 'function':  // function public, function _protected
        while (T_STRING !== $tok[0]) $tok= $t->getNextToken();
        // var_dump($tok);
        $out[]= ('_' == $tok[1]{0} && '_' != $tok[1]{1}) ? 'protected' : 'public';
        if (
          ('getInstance' == $tok[1]) ||
          ('fromString' == $tok[1]) ||
          ('fromFile' == $tok[1])
        ) $out[]= ' static';
        $out[]= ' function ';
        break;
      
      case '$this':     // $this->setURI();
        $following= array();
        for ($i= 0; $i < 3; $i++) {
          $following[]= $t->getNextToken();
        }
        
        if (
          (T_OBJECT_OPERATOR === $following[0][0]) &&
          (T_STRING === $following[1][0]) &&
          ('(' === $following[2][1])
        ) {
          $out[]= 'self::'.$following[1][1].'(';
        } else {
          $out[]= '$this->'.$following[1][1].$following[2][1];
        }
        $tok= array(T_NONE, '');
        break;
       
      case 'try':       // try(); {
        while ('{' !== $tok[1]) $tok= $t->getNextToken();
        $out[]= 'try ';
        break;
      
      case 'new':       // $handler= &new $reflect();
        while ('(' !== $tok[1]) {
          if ('$' == $tok[1]{0}) {
            array_pop($out);    // whitespace
            array_pop($out);    // "new" keyword
            $out[]= 'lang::XPClass::forName('.$tok[1].')->newInstance';
          } else {
            $out[]= $tok[1];
          }
          $tok= $t->getNextToken();
        }
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
              $out[]= 'lang::'.substr($tok[1], 1, -1).' ';
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
    
    $out[]= str_replace("\r", '', $tok[1]);
  } while ($tok= $t->getNextToken());

  $f= &new File('php://stdout');
  try(); {
    $f->open(FILE_MODE_WRITE);
    $f->write($header);
    if ($uses) {
      $f->write("uses(\n  ".implode(",\n  ", $uses)."\n);\n\n");
    }
    $f->write('namespace '.$namespace." {\n\n  ");
    $f->write(str_replace('= &', '= ', trim(chop(implode('', $out)))));
    $f->write("\n}\n?>");
    $f->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  // }}}
?>
