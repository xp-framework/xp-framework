<?php
/* This file provides the core for the XP framework
 * 
 * $Id$
 */

  namespace xp {
    const integer = 'integer';
    const float   = 'float';
    const string  = 'string';
    const bool    = 'bool';
    const null    = 'null';
    const void    = 'void';
    
    class registry {
      public static 
        $names     = array(),
        $errors    = array();
    }
    
    // {{{ proto &mixed cast (&mixed var, type type)
    //     Casts a variable "null-safe", that is, variables that are NULL
    //     will not be touched. The argument type is one of xp::void,
    //     xp::integer, xp::double, xp::string, xp::bool or xp::null
    function &cast(&$var, $type) {
      if (NULL === $var) return NULL;

      switch ($type) {
        case xp::void: 
          return $var;

        case xp::integer:
        case xp::double:
        case xp::string:
        case xp::bool:
        case xp::null:
          settype($var, $type);
          return $var;
      }
      
      throw new lang::IllegalArgumentException(
        'Argument 2 ['.var_export($type, 1).'] is not a recognized type'
      );
    }
    // }}}
    
    // {{{ proto string[] seperatecn (string name)
    //     Takes a xp-style fqcn and explodes it into namespace and class
    //     e.g. de.thekid.forum.Entry will become namespace = 
    //     "de.thekid.forum" and class = "Entry"
    function seperatecn($name) {
      $p= strrpos($name, '.');
      return array(substr($name, 0, $p), substr($name, $p+ 1));
    }
    // }}}
    
    // {{{ proto void uses(string* args)
    //     A "variant" of include_once that takes xp-style fqcns as arguments
    //     e.g. uses('de.thekid.forum.Entry')
    function uses() {
      foreach (func_get_args() as $arg) {
        list($namespace, $class)= xp::seperatecn($arg);
        if (!include_once(
          str_replace('.', DIRECTORY_SEPARATOR, $namespace).
          DIRECTORY_SEPARATOR.
          $class.'.class.php')
        ) continue;

        xp::registry::$names[strtr($namespace, '.', ':').'::'.strtolower($class)]= $arg;
      }
    }
    // }}}

    // {{{ proto void error(int no, string file, string file, int line)
    //     Error handler callback for php://set_error_handler
    function error($no, $str, $file, $line) {
      if (0 != error_reporting()) xp::registry::$errors[$file][]= array($no, $str, $line);
    }
    // }}}
  }

  import function uses from xp;
  import function error from xp;
  
  error_reporting(E_ALL);
  define('SKELETON_PATH', dirname(__FILE__));
  ini_set('include_path', SKELETON_PATH.':'.ini_get('include_path'));
  set_error_handler('error');
  
  uses(
    'lang.Object',
    'lang.Exception',
    'lang.XPClass',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.FormatException',
    'lang.ClassLoader'
  );
?>
