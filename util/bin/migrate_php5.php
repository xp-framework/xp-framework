<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses(
    'util.cmd.ParamString', 
    'lang.System',
    'io.File',
    'io.Folder',
    'io.FileUtil',
    'text.PHPTokenizer'
  );

  // Class names that need to be mapped due to built-in classes in PHP5.
  $map= array(
    'Exception'        => 'XPException',
    'Iterator'         => 'XPIterator',
    'lang.Exception'   => 'lang.XPException',
    'util.Iterator'    => 'util.XPIterator'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (2 > $p->count || $p->exists('help', '?')) {
    printf(
      "Usage:   %1\$s <filename> [--target=<target_dir>] [--dump]\n".
      "Example: for i in `find ~/devel/xp/skeleton/ -type f -name '*.class.php' | grep -v /lang/` ; do php migrate_php5.php \$i --target=~/devel/xp/experiments/skeleton2/; done\n",
      basename($p->value(0))
    );
    exit(-2);
  }
  $filename= realpath($p->value(1));

  // Calculate namespace
  preg_match('#skeleton/([a-z0-9/-]+)/([^\.]+)\.class\.php$#', $filename, $matches);
  $path= $matches[1];

  // Tokenize
  $t= &new PHPTokenizer();
  try(); {
    $t->setTokenString(FileUtil::getContents(new File($filename)));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
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
  
  $class= 0; $abstract= 0;
  $uses= $constants= $implements= $apidoc= array();
  do {
    if (T_CLOSE_TAG === $tok[0]) break;
    
    if ((T_COMMENT === $tok[0]) && ('/**' == substr($tok[1], 0, 3))) {
      $apidoc= array();
      foreach (preg_split('/[\r\n]([\s\t]*\* ?)?/', $tok[1]) as $line) {
        if ('@' != $line{0}) continue;
        $args= preg_split('/[\s\t]+/', substr($line, 1), 2);
        switch ($args[0]) {
          case 'param':
            list($type, $name)= explode(' ', $args[1]);
            $apidoc['params'][$name]= $type;
            break;

          default:
            $apidoc[$args[0]]= isset($args[1]) ? $args[1] : TRUE;
        }
      }
    }
    switch (strtolower($tok[1])) {
      case 'uses':
        while (')' !== $tok[1]) {
          if (T_CONSTANT_ENCAPSED_STRING === $tok[0]) {
            $name= trim($tok[1], '"\'');
            $uses[]= "'".(isset($map[$name]) ? $map[$name] : $name)."'";
          }
          $tok= $t->getNextToken();
        }
        $tok= $t->getNextToken(); // Swallow ";"
        $tok= array(T_NONE, '');
        break;

      case 'implements':
        while (')' !== $tok[1]) {
          if (T_CONSTANT_ENCAPSED_STRING === $tok[0]) {
            $name= trim($tok[1], '"\'');
            $implements[]= "'".(isset($map[$name]) ? $map[$name] : $name)."'";
          }
          $tok= $t->getNextToken();
        }
        $tok= $t->getNextToken(); // Swallow ";"
        $tok= array(T_NONE, '');
        break;
        
      case 'parent':        // Object no longer has constructors
        $parentcall= '';
        $remove= FALSE;
        while (';' !== $tok[1]) {
          $parentcall.= $tok[1];
          $tok= $t->getNextToken();
          if (
            ('__construct' == $tok[1]) && 
            ('Object' == $extends)
          ) {
            $remove= TRUE;
          }
        }
        $out[]= $remove ? '' : $parentcall.';';
        $tok= array(T_NONE, '');
        break;
      
      case 'class':        // class StdStream extends Object {
        $out[]= 'class ';
        $class_decl= sizeof($out)- 1;
        $t->getNextToken();             // Swallow whitepsace
        $classname= $t->getNextToken();
        $extends= NULL;
        $out[]= isset($map[$classname[1]]) ? $map[$classname[1]] : $classname[1];
        while ('{' !== $tok[1]) {
          $tok= $t->getNextToken();
          $out[]= $tok[1];
          
          if ('extends' == $tok[1]) {
            $tok= $t->getNextToken();   // Swallow whitespace
            $out[]= $tok[1];
            $tok= $t->getNextToken();
            $extends= $tok[1];
            $out[]= isset($map[$tok[1]]) ? $map[$tok[1]] : $tok[1];
          }
        }
        
        if ('Interface' == $extends) {
          $out[sizeof($out) - 8]= 'interface ';
          
          // Remove "extends Interface"
          do {
            unset($out[sizeof($out) - 2]);
          } while ('extends' != $out[sizeof($out) - 2]);
          unset($out[sizeof($out) - 2]);
        }
        $class= sizeof($out)- 1;
        $tok= array(T_NONE, '');
        break;
        
      case 'var':
        $variables= array(
          'protected' => array(),
          'public'    => array(),
        );
        while (';' !== $tok[1]) {
          if (T_VARIABLE == $tok[0]) {
            $variable= $tok[1];
            $v= array();
            $l= 0;
            while ($l > -1) {
              $tok= $t->getNextToken();
              switch ($tok[1]) {
                case '(': $l++; break;
                case ')': $l--; break;
                case ',': 
                case ';': if (0 == $l) break 2;
              }
              $v[]= $tok[1];
            }
            $variables['_' == $variable{1} ? 'protected' : 'public'][$variable]= $v;
          } else {
            $tok= $t->getNextToken();
          }
        }
        foreach ($variables as $type => $list) {
          if (empty($list)) continue;
          $out[]= $type;
          foreach ($list as $variable => $initialization) {
            $out[]= "\n      ".$variable.implode('', $initialization).',';
          }
          $out[]= rtrim(array_pop($out), ',;').';';
          $out[]= "\n    ";
        }
        array_pop($out);            // Get rid of trailing whitespace
        $tok= array(T_NONE, '');
        break;
        
      case 'function':  // function public, function _protected
        while (T_STRING !== $tok[0]) $tok= $t->getNextToken();
        $function= $tok[1];
        
        // Use @access API-Doc if available, guess otherwise
        switch (@$apidoc['access']) {
          case 'public':
          case 'protected':
            $out[]= $apidoc['access'];
            break;

          case 'private':       // Make this protected, private is often misused
            $out[]= 'protected';
            break;
            
          case 'static':        // Incorrect API doc, it should be @model static
            $out[]= 'public static';
            break;

          default:
            $out[]= ('_' == $function{0} && '_' != $function{1}) ? 'protected' : 'public';
        }
        
        // Check static, abstract and final models
        if (@$apidoc['model'] == 'static') $out[]= ' static';
        if (@$apidoc['model'] == 'abstract') $out[]= ' abstract';
        if (@$apidoc['model'] == 'final') $out[]= ' final';
        if (@$apidoc['model'] == 'abstract') $abstract++;
        
        $out[]= ' function ';

        // Skip until parameters begin
        while ('(' !== $tok[1]) {
          $out[]= $tok[1];
          $tok= $t->getNextToken();
        }
        
        // Add type hints if applicable, strip off reference operator
        while (')' !== $tok[1]) {
          if ('&' == $tok[1]) {
            $tok= $t->getNextToken();
          }
          if (T_VARIABLE == $tok[0]) {
            $type= @$apidoc['params'][substr($tok[1], 1)];
            if ((FALSE !== strpos($type, '.')) && (FALSE === strpos($type, '['))) {
              $typename= ltrim(substr($type, strrpos($type, '.')+ 1), '&');
              $out[]= (isset($map[$typename]) ? $map[$typename] : $typename).' ';
            }
          }
          $out[]= $tok[1];
          $tok= $t->getNextToken();
        }

        // Skip until method body
        while ('{' !== $tok[1]) {
          $out[]= $tok[1];
          $tok= $t->getNextToken();
        }
        
        // Kill method body if this function resides in an interface or is declared 
        // abstract. Change getInstance() method to use class statics
        if (('Interface' == $extends) || (@$apidoc['model'] == 'abstract')) {
          while ('}' !== $tok[1]) {
            $tok= $t->getNextToken();
          }
          array_pop($out);  // Kill whitespace after closing ")"
          $out[]= ';';
          $tok= $t->getNextToken();
        } elseif ('getInstance' == $function) {
          $static= NULL;
          while ('}' !== $tok[1]) {
            if (!$static && T_STATIC == $tok[0]) {
              $t->getNextToken();              // Swallow whitespace
              $tok= $t->getNextToken();        // Static variable name
              $static= $tok[1];
              while (';' !== $tok[1]) {
                $tok= $t->getNextToken();
              }
              $t->getNextToken();
              $tok= $t->getNextToken();
            }
            $out[]= $static ? str_replace($static, 'self::'.$static, $tok[1]) : $tok[1];
            $tok= $t->getNextToken();
          }
          
          // Now insert instance variable
          if ($static) {
            $out= array_merge(
              array_slice($out, 0, $class+ 3),
              array('protected static '.$static."= NULL;\n    "),
              array_slice($out, $class+ 3)
            );
          }
        }
        
        // Reset API-doc
        $apidoc= array();
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
            $out[]= 'XPClass::forName('.$tok[1].')->newInstance';
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
        while ($token= strtolower(array_pop($out))) {
          if ('elseif' == $token || 'if' == $token) break;
        }
        $out[]= 'catch (';
        while ('{' !== $tok[1]) {
          switch ($tok[0]) {
            case T_CONSTANT_ENCAPSED_STRING:    // Exception name
              $name= substr($tok[1], 1, -1);
              $out[]= (isset($map[$name]) ? $map[$name] : $name).' ';
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
  
  // Make class abstract if it contains more than one abstract method
  if ($abstract) {
    printf("---> Making class abstract, it contains %d abstract methods\n", $abstract);
    $out[$class_decl]= 'abstract class ';
  }
  
  // Add implements clause
  if (!empty($implements)) {
    $ilist= '';
    foreach ($implements as $i) {
      $name= substr($i, strrpos($i, '.')+ 1, -1);
      $ilist.= ', '.(isset($map[$name]) ? $map[$name] : $name);
    }
    $out[$class]= 'implements '.substr($ilist, 2).' {';
    $uses= array_merge($uses, $implements);
  }

  // Print target file
  $target= str_replace('//', '/', sprintf(
    '%s/%s/%s.class.php',
    str_replace('~', System::getProperty('user.home'), $p->value('target', 't', '.')),
    $path, 
    isset($map[$classname[1]]) ? $map[$classname[1]] : $classname[1]
  ));
  printf("===> Target: %s\n", $target);
  
  // --dump will show the file's contents rather than saving it
  if ($p->exists('dump')) {
    $target= 'php://stdout';
  } else {
  
    // Ensure directory exists
    $folder= &new Folder(dirname($target));
    if (!$folder->exists()) {
      try(); {
        printf("---> Creating folder %s\n", $folder->getURI());
        $folder->create();
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit(-1);
      }
    }
  }

  // Finally, write file
  $f= &new File($target);
  try(); {
    $f->open(FILE_MODE_WRITE);
    $f->write($header);
    switch (sizeof($uses)) {
      case 0: break;
      case 1: $f->write('  uses('.$uses[0].");\n\n"); break;
      default: $f->write("  uses(\n    ".implode(",\n    ", $uses)."\n  );\n\n");
    }
    $f->write('  '.str_replace('= &', '= ', trim(chop(implode('', $out)))));
    $f->write("\n?>\n");
    $f->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  printf("===> %d bytes written\n", $f->size());  
  // }}}
?>
