<?php
/* This file provides the generic uses wrapper for the XP framework
 * 
 * $Id$
 */

  uses('InstantiationException');

  define('GENERIC_PARSER_ST_INITIAL',     'initial');
  define('GENERIC_PARSER_ST_DECL',        'decl');
  define('GENERIC_PARSER_ST_GENERICS',    'generics');
  define('GENERIC_PARSER_ST_USES',        'uses');
  define('GENERIC_PARSER_ST_BODY',        'body');
  define('GENERIC_PARSER_ST_METHOD_DECL', 'method');
  define('GENERIC_PARSER_ST_METHOD_ARGS', 'method.args');

  // {{{ lang.Object create(string spec)
  //     Creates a generic object
  function create($spec) {
    sscanf($spec, '%[^<]<%[^>]>', $classname, $types);
    $components= explode(',', $types);
    $class= xp::reflect($classname);
    $tokens= uwrp·generic::$tokens[$class];
    
    // Sanity check components and tokens
    if (sizeof($tokens) != sizeof($components)) {
      throw new InstantiationException('Incorrect number of component types');
    }
    
    // Instanciate without invoking the constructor
    $__id= microtime();
    $instance= unserialize('O:'.strlen($class).':"'.$class.'":1:{s:4:"__id";s:'.strlen($__id).':"'.$__id.'";}');

    // Pass types
    foreach ($components as $i => $type) {
      $instance->__types[$tokens[$i]]= trim($type);
    }
    
    // Call constructor if available
    if (is_callable(array($instance, '__construct·'))) {
      $a= func_get_args();
      call_user_func_array(array($instance, '__construct·'), array_slice($a, 1));
    }
    
    return $instance;
  }
  // }}}

  // {{{ final class uwrp·generic
  //     Stream wrapper
  final class uwrp·generic {
    public static
      $tokens     = array();

    protected
      $buffer     = '',
      $offset     = 0;

    // {{{ void verify(lang.Object object, string method, array<string, mixed> arguments)
    //     Verifies method arguments
    public static function verify($object, $method, $arguments) {
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
        
        throw new IllegalArgumentException(sprintf(
          'Type mismatch for %s in %s::%s() (was: %s, expecting: %s)',
          $token,
          $object->getClassName(),
          $method,
          xp::typeOf($arguments[$token]),
          $object->__types[$token]
        ));
      }
    }
    // }}}

    // {{{ bool stream_open(string path, string mode, int options, &string open)
    //     Open wrapper
    function stream_open($path, $mode, $options, &$open) {
      $url= parse_url($path);
      
      $file= strtr($url['host'], '.', DIRECTORY_SEPARATOR).'.class.php';
      $line= 1;
      $tokens= token_get_all(file_get_contents($file));
      $state= GENERIC_PARSER_ST_INITIAL;
      $bracket= '';
      $generics= array();
      $type= $ctype= NULL;
      $this->buffer= '';
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($state.$tokens[$i][0]) {
          case GENERIC_PARSER_ST_INITIAL.T_STRING:
            if ('uses' == $tokens[$i][1]) {
              $state= GENERIC_PARSER_ST_USES;
            }
            break;

          case GENERIC_PARSER_ST_USES.T_CONSTANT_ENCAPSED_STRING:
            uses(trim($tokens[$i][1], "'"));
            break;

          case GENERIC_PARSER_ST_USES.'(':
          case GENERIC_PARSER_ST_USES.')':
          case GENERIC_PARSER_ST_USES.',':
          case GENERIC_PARSER_ST_USES.T_WHITESPACE:
            // Intentionally empty
            break;

          case GENERIC_PARSER_ST_USES.';':
            $state= GENERIC_PARSER_ST_INITIAL;
            break;
            
          case GENERIC_PARSER_ST_INITIAL.T_CLASS:
          case GENERIC_PARSER_ST_INITIAL.T_INTERFACE:
            $ctype= $tokens[$i][1];
            $this->buffer.= $tokens[$i][1];
            $class= $tokens[$i+ 2][1];
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

          case GENERIC_PARSER_ST_DECL.T_IMPLEMENTS:
            if (empty($generics)) {

              // Class is not generic and implements a generic interface
              $ctype= 'ordinary';
              $interface= $tokens[$i+ 2][1];
            }
            $this->buffer.= 'implements';
            break;
            
          case GENERIC_PARSER_ST_DECL.'{':
            $this->buffer.= '{';
            if ('class' == $ctype) {
              $this->buffer.= (
                "\n    public \$__types= array();".
                "\n    public function __construct() {".
                "\n      throw new InstantiationException('Cannot be instantiated directly');".
                "\n    }"
              );
            } else if ('ordinary' == $ctype) {
              if (sizeof($generics) != sizeof(uwrp·generic::$tokens[$interface])) {
                throw new Error('Generic interface '.$interface.' not correctly implemented');
              }
            }
            $state= GENERIC_PARSER_ST_BODY;
            break;

          case GENERIC_PARSER_ST_BODY.T_FUNCTION:
            $this->buffer.= 'function';
            $state= GENERIC_PARSER_ST_METHOD_DECL;
            break;

          case GENERIC_PARSER_ST_METHOD_DECL.T_STRING:
            $method= $tokens[$i][1];
            if ('class' == $ctype && '__construct' == $method) $method= '__construct·';
            $this->buffer.= $method;
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
            if ('class' == $ctype && !empty($arguments)) {
              $this->buffer.= ' uwrp·generic::verify($this, \''.$method.'\', array(';
              foreach ($arguments as $name => $token) {
                $this->buffer.= "'".$token."' => ".$name.', ';
              }
              $this->buffer.= '));';
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
      
      uwrp·generic::$tokens[xp::reflect($url['host'])]= array_keys($generics);
      
      // echo '== ', $url['host'], ' ==', "\n";
      // var_dump($generics);
      // echo $this->buffer;
      // echo str_repeat('-', 72), "\n";
      
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
