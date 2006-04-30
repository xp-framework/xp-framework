<?php
/* This file provides the generic uses wrapper for the XP framework
 * 
 * $Id$
 */

  define('GENERIC_PARSER_ST_INITIAL',     'initial');
  define('GENERIC_PARSER_ST_DECL',        'decl');
  define('GENERIC_PARSER_ST_GENERICS',    'generics');
  define('GENERIC_PARSER_ST_BODY',        'body');
  define('GENERIC_PARSER_ST_METHOD_DECL', 'method');
  define('GENERIC_PARSER_ST_METHOD_ARGS', 'method.args');

  // {{{ &lang.Object create(string spec)
  //     Creates a generic object
  function &create($spec) {
    sscanf($spec, '%[^<]<%[^>]>', $classname, $types);

    $class= xp::reflect($classname);
    $tokens= uwrp·generic::tokens($class);
    $instance= &new $class();

    // Pass types
    foreach (explode(',', $types) as $i => $type) {
      $instance->__types[$tokens[$i]]= trim($type);
    }
    
    // Call rewritten constructor if existant.
    if (method_exists($instance, '__generic')) {
      $a= func_get_args();
      call_user_func_array(array(&$instance, '__generic'), array_slice($a, 1));
    }
    
    return $instance;
  }
  // }}}

  // {{{ final class uwrp·generic
  //     Stream wrapper
  class uwrp·generic {
    var
      $buffer     = '',
      $offset     = 0;

    // {{{ string[] tokens(string classname, string[] tokens= NULL)
    //     Stores class / token mapping data
    function tokens($classname, $tokens= NULL) {
      static $registry= array();
      
      if (!isset($tokens)) return $registry[$classname];
      $registry[$classname]= $tokens;
    }
    // }}}
    
    // {{{ bool verify(&lang.Object object, string method, array<string, mixed> arguments)
    //     Verifies method arguments
    function verify(&$object, $method, $arguments) {
      foreach (array_keys($arguments) as $token) {
        switch ($object->__types[$token]) {
          case 'int': $r= is_int($arguments[$token]); break;
          case 'float': $r= is_float($arguments[$token]); break;
          case 'string': $r= is_string($arguments[$token]); break;
          case 'null': $r= is_null($arguments[$token]); break;
          case 'bool': $r= is_bool($arguments[$token]);
          case 'array': $r= is_array($arguments[$token]); break;
          default: $r= is($object->__types[$token], $arguments[$token]); break;
        }

        if ($r) continue;
        
        return throw(new IllegalArgumentException(
          $object->getClassName().'::'.$method.': Wrong type for '.$token.' (was '.xp::typeOf($arguments[$token]).', expecting '.$object->__types[$token].')'
        ));
      }
      
      return TRUE;
    }
    // }}}

    // {{{ bool stream_open(string path, string mode, int options, &string open)
    //     Open wrapper
    function stream_open($path, $mode, $options, &$open) {
      $url= parse_url($path);
      
      $file= strtr($url['host'], '.', DIRECTORY_SEPARATOR).'.class.php';
      $line= 0;
      $tokens= token_get_all(file_get_contents($file));
      $state= GENERIC_PARSER_ST_INITIAL;
      $bracket= '';
      $this->buffer= '';
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($state.$tokens[$i][0]) {
          case GENERIC_PARSER_ST_INITIAL.T_CLASS:
            $this->buffer.= 'class';
            $state= GENERIC_PARSER_ST_DECL;
            break;
          
          case GENERIC_PARSER_ST_DECL.'<':
            $state= GENERIC_PARSER_ST_GENERICS;
            $generics= array();
            break;

          case GENERIC_PARSER_ST_GENERICS.T_STRING:
            $generics[$tokens[$i][1]]= 1;
            break;

          case GENERIC_PARSER_ST_GENERICS.',':
          case GENERIC_PARSER_ST_GENERICS.T_WHITESPACE:
            // Intentionally empty
            break;

          case GENERIC_PARSER_ST_GENERICS.'>':
            $state= GENERIC_PARSER_ST_DECL;
            break;
            
          case GENERIC_PARSER_ST_DECL.'{':
            $this->buffer.= "{\n    var \$__types= array();";
            $state= GENERIC_PARSER_ST_BODY;
            break;

          case GENERIC_PARSER_ST_BODY.T_FUNCTION:
            $this->buffer.= 'function';
            $state= GENERIC_PARSER_ST_METHOD_DECL;
            break;

          case GENERIC_PARSER_ST_METHOD_DECL.T_STRING:
            $method= $tokens[$i][1];
            $this->buffer.= '__construct' == $method ? '__generic' : $method;
            break;

          case GENERIC_PARSER_ST_METHOD_DECL.'(':
            $this->buffer.= '(';
            $state= GENERIC_PARSER_ST_METHOD_ARGS;
            $arguments= array();
            $type= NULL;
            break;

          case GENERIC_PARSER_ST_METHOD_ARGS.T_STRING:
            $type= $tokens[$i][1];
            isset($generics[$type]) || xp::error(sprintf(
              'Unknown token "%s" in declaration of %s::%s(...) in %s on line %d',
              $type,
              $url['host'],
              $method,
              $file,
              $line
            ));
            $i++; // Swallow following whitespace
            break;

          case GENERIC_PARSER_ST_METHOD_ARGS.T_VARIABLE:
            $type && $arguments[$tokens[$i][1]]= $type;
            $type= NULL;
            $this->buffer.= $tokens[$i][1];
            break;

          case GENERIC_PARSER_ST_METHOD_ARGS.')':
            $this->buffer.= ')';
            $state= GENERIC_PARSER_ST_METHOD_DECL;
            break;
            
          case GENERIC_PARSER_ST_METHOD_DECL.'{':
            $this->buffer.= '{';
            if (!empty($arguments)) {
              $this->buffer.= ' if (!uwrp·generic::verify($this, \''.$method.'\', array(';
              foreach ($arguments as $name => $token) {
                $this->buffer.= "'".$token."' => &".$name.', ';
              }
              $this->buffer.= '))) return;';
            }
            $state= GENERIC_PARSER_ST_BODY;
            break;
          
          default:
            if (is_array($tokens[$i])) {
              $line+= substr_count($tokens[$i][1], "\n");
              $this->buffer.= $tokens[$i][1];
            } else {
              $this->buffer.= $tokens[$i];
            }
        }
      }
      
      uwrp·generic::tokens(xp::reflect($url['host']), array_keys($generics));
      // DEBUG var_dump($url['host'], $generics, $this->buffer);
      return TRUE;
    }  
    // }}}

    // {{{ string stream_read(int count)
    //     Read wrapper
    function stream_read($count) {
      $chunk= substr($this->buffer, $this->offset, $count);
      $this->offset+= $count;
      return $chunk;
    }
    // }}}

    // {{{ bool stream_eof(void)
    //     EOF wrapper
    function stream_eof() {
      return $this->offset > strlen($this->buffer);
    }
    // }}}
  }
  // }}}
?>
