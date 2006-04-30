<?php
/* This file provides the generic uses wrapper for the XP framework
 * 
 * $Id$
 */

  define('GENERIC_PARSER_ST_INITIAL',  'initial');
  define('GENERIC_PARSER_ST_DECL',     'decl');
  define('GENERIC_PARSER_ST_GENERICS', 'generics');

  // {{{ &lang.Object create(string spec)
  //     Creates a generic object
  function &create($spec) {
    sscanf($spec, '%[^<]<%[^>]>', $classname, $types);

    $class= xp::reflect($classname);
    $tokens= uwrp·generic::tokens($class);

    // Pass arguments and instanciate
    for ($args= func_get_args(), $paramstr= '', $i= 1, $m= sizeof($args); $i < $m; $i++) {
      $paramstr.= ', $args['.$i.']';
    }
    eval('$instance= &new '.$class.'('.substr($paramstr, 2).');');

    // Pass types
    foreach (explode(',', $types) as $i => $type) {
      $instance->__types[$tokens[$i]]= trim($type);
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

    // {{{ bool stream_open(string path, string mode, int options, &string open)
    //     Open wrapper
    function stream_open($path, $mode, $options, &$open) {
      $url= parse_url($path);
      
      $tokens= token_get_all(file_get_contents(strtr($url['host'], '.', DIRECTORY_SEPARATOR).'.class.php'));
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
            $generics[]= $tokens[$i][1];
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
            $state= GENERIC_PARSER_ST_INITIAL;
            break;
          
          default:
            $this->buffer.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
        }
      }
      
      uwrp·generic::tokens(xp::reflect($url['host']), $generics);
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
