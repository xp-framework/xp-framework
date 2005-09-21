<?php
  // {{{ lang.Object
  class Object {
    var $__id;
    
    function Object() {
      $this->__id= microtime();
      if (!method_exists($this, '__construct')) return;
      $args= func_get_args();
      call_user_func_array(array(&$this, '__construct'), $args);
    }

    function hashCode() {
      return $this->__id;
    }
    
    function equals(&$cmp) {
      return $this === $cmp;
    }
    
    function __destruct() {
      unset($this);
    }
    
    function getClassName() {
      return xp::nameOf(get_class($this));
    }

    function &getClass() {
      return new XPClass($this);
    }

    function toString() {
      return $this->getClassName().'@'.var_export($this, 1);
    }
  }
  xp::registry('class.object', 'lang.Object');
  // }}}
  // {{{ lang.Throwable
  class Throwable extends Object {
    var 
      $message  = '',
      $trace    = array();

    function __construct($message) {
      static $except= array(
        'call_user_func_array', 'call_user_func', 'object', '__call', '__set', '__get'
      );
      $this->message= $message;
      
      $errors= xp::registry('errors');
      foreach (debug_backtrace() as $trace) {
        if (!isset($trace['function']) || in_array($trace['function'], $except)) continue;
        // Pop error messages off the copied error stack
        if (isset($trace['file']) && isset($errors[$trace['file']])) {
          $messages= $errors[$trace['file']];
          unset($errors[$trace['file']]);
        } else {
          $messages= array();
        }
        // Not all of these are always set: debug_backtrace() should
        // initialize these - at least - to NULL, IMO => Workaround.
        $this->trace[]= &new StackTraceElement(
          isset($trace['file']) ? $trace['file'] : NULL,
          isset($trace['class']) ? $trace['class'] : NULL,
          isset($trace['function']) ? $trace['function'] : NULL,
          isset($trace['line']) ? $trace['line'] : NULL,
          isset($trace['args']) ? $trace['args'] : NULL,
          $messages
        );
      }
      
      // Remaining error messages
      foreach (array_keys($errors) as $key) {
        $class= ('.class.php' == substr($key, -10)
          ? strtolower(substr(basename($key), 0, -10))
          : '<main>'
        );
        for ($i= 0, $s= sizeof($errors[$key]); $i < $s; $i++) { 
          $this->trace[]= &new StackTraceElement(
            $key,
            $class,
            NULL,
            $errors[$key][$i][2],
            array(),
            array($errors[$key][$i])
          );
        }
      }
    }

    function getMessage() {
      return $this->message;
    }

    function getStackTrace() {
      return $this->trace;
    }

    function printStackTrace($fd= STDERR) {
      fputs($fd, $this->toString());
    }
 
    function toString() {
      $s= sprintf(
        "Exception %s (%s)\n",
        $this->getClassName(),
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
  xp::registry('class.throwable', 'lang.Throwable');
  // }}}
  // {{{ lang.StackTraceElement
  class StackTraceElement extends Object {
    var
      $file     = '',
      $class    = '',
      $method   = '',
      $line     = 0,
      $args     = array(),
      $messages = array();
      
    function __construct($file, $class, $method, $line, $args, $messages) {
      $this->file     = $file;  
      $this->class    = $class; 
      $this->method   = $method;
      $this->line     = $line;
      $this->args     = $args;
      $this->messages = $messages;
    }
    
    function toString() {
      $args= array();
      if (isset($this->args)) {
        for ($j= 0, $a= sizeof($this->args); $j < $a; $j++) {
          if (is_array($this->args[$j])) {
            $args[]= 'array['.sizeof($this->args[$j]).']';
          } elseif (is_object($this->args[$j])) {
            $args[]= get_class($this->args[$j]).'{}';
          } elseif (is_string($this->args[$j])) {
            $display= str_replace('%', '%%', addcslashes(substr($this->args[$j], 0, min(
              (FALSE === $p= strpos($this->args[$j], "\n")) ? 0x40 : $p, 
              0x40
            )), "\0..\17"));
            $args[]= (
              '(0x'.dechex(strlen($this->args[$j])).")'".
              $display.
              "'"
            );
          } elseif (is_null($this->args[$j])) {
            $args[]= 'NULL';
          } else {
            $args[]= (string)$this->args[$j];
          }
        }
      }
      $fmt= sprintf(
        "  at %s::%s(%s) [line %%3\$d of %s] %%2\$s\n",
        isset($this->class) ? xp::nameOf($this->class) : '<main>',
        isset($this->method) ? $this->method : '<main>',
        implode(', ', $args),
        basename(isset($this->file) ? $this->file : __FILE__)
      );
      
      if (!$this->messages) {
        return sprintf(
          $fmt, 
          E_USER_NOTICE, 
          '', 
          isset($this->line) ? $this->line : __LINE__
        );
      }
      
      $str= '';
      for ($i= 0, $s= sizeof($this->messages); $i < $s; $i++) {
        $str.= rtrim(vsprintf($fmt, $this->messages[$i]))."\n";
      }
      return $str;
    }
  
  }
  xp::registry('class.stacktraceelement', 'lang.StackTraceElement');
  // }}}
  // {{{ lang.Error
  class Error extends Throwable {
     
  }
  xp::registry('class.error', 'lang.Error');
  // }}}
  // {{{ lang.Exception
  class Exception extends Throwable {
     
  }
  xp::registry('class.exception', 'lang.Exception');
  // }}}
  // {{{ lang.Interface 
  class Interface {
  
    function Interface() {
      xp::error('Interfaces cannot be instantiated');
    }
  }
  xp::registry('class.interface', 'lang.Interface');
  // }}}
  // {{{ lang.XPClass
  define('MODIFIER_STATIC',       1);
  define('MODIFIER_ABSTRACT',     2);
  define('MODIFIER_FINAL',        4);
  define('MODIFIER_PUBLIC',     256);
  define('MODIFIER_PROTECTED',  512);
  define('MODIFIER_PRIVATE',   1024);
  
  define('DETAIL_MODIFIERS',      0);
  define('DETAIL_ARGUMENTS',      1);
  define('DETAIL_RETURNS',        2);
  define('DETAIL_THROWS',         3);
  define('DETAIL_COMMENT',        4);
  define('DETAIL_ANNOTATIONS',    5);
  define('DETAIL_NAME',           6);
 
  class XPClass extends Object {
    var 
      $_objref  = NULL,
      $name     = '';
      
    function __construct(&$ref) {
      $this->_objref= &$ref;
      $this->name= xp::nameOf(is_object($ref) ? get_class($ref) : $ref);
    }

    function equals(&$cmp) {
      return (is_a($cmp, 'XPClass') 
        ? 0 == strcmp($this->getName(), $cmp->getName())
        : FALSE
      );
    }
    
    function getName() {
      return $this->name;
    }
    
    function &newInstance() {
      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      return eval('return new '.xp::reflect($this->name).'('.substr($paramstr, 2).');');
    }
    
    function _methods() {
      $methods= array_flip(get_class_methods($this->_objref));
      
      // Well-known methods
      unset($methods['__construct']);
      unset($methods['__destruct']);
      // "Inherited" constructors
      $c= is_object($this->_objref) ? get_class($this->_objref) : $this->_objref;
      do {
        unset($methods[$c]);
      } while ($c= get_parent_class($c));
      return array_keys($methods);
    }
    
    function getMethods() {
      $m= array();
      foreach ($this->_methods() as $method) {
        $m[]= &new Method($this->_objref, $method);
      }
      return $m;
    }

    function &getMethod($name) {
      if (!$this->hasMethod($name)) return NULL;
      return new Method($this->_objref, $name); 
    }
    
    function hasMethod($method) {
      return in_array(strtolower($method), $this->_methods());
    }
    
    function hasConstructor() {
      return in_array('__construct', get_class_methods($this->_objref));
    }
    
    function &getConstructor() {
      if ($this->hasConstructor()) {
        return new Constructor($this->_objref); 
      }
      return NULL;
    }
    
    function getFields() {
      $f= array();
      foreach ((is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      ) as $field => $value) {
        if ('__id' == $field) continue;
        $f[]= &new Field($this->_objref, $field, isset($value) ? gettype($value) : NULL);
      }
      return $f;
    }
    
    function &getField($name) {
      if (!$this->hasField($name)) return NULL;
      $v= (is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      );
      return new Field($this->_objref, $name, isset($v[$name]) ? gettype($v[$name]) : NULL);
    }
    
    function hasField($field) {
      return '__id' == $field ? FALSE : array_key_exists($field, is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      );
    }

    function &getParentclass() {
      if (!($p= get_parent_class($this->_objref))) return NULL;
      return new XPClass($p);
    }
    
    function isSubclassOf($name) {
      $cmp= xp::reflect($this->name);
      $name= xp::reflect($name);
      while ($cmp= get_parent_class($cmp)) {
        if ($cmp == $name) return TRUE;
      }
      return FALSE;
    }
    
    function isInstance(&$obj) {
      return is($this->name, $obj);
    }
    
    function isInterface() {
      return $this->isSubclassOf('lang.Interface');
    }
    
    function getInterfaces() {
      $r= array();
      $c= xp::reflect($this->name);
      $implements= xp::registry('implements');
      if (isset($implements[$c])) foreach (array_keys($implements[$c]) as $iface) {
        $r[]= &new XPClass($iface);
      }
      return $r;
    }

    function hasAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForClass($this->name);
      return $details && ($key 
        ? array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
      );
    }

    function getAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForClass($this->name);
      if (!$details || !($key 
        ? array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
      )) return raise(
        'lang.ElementNotFoundException', 
        'Annotation "'.$name.($key ? '.'.$key : '').'" does not exist'
      );
      return ($key 
        ? $details['class'][DETAIL_ANNOTATIONS][$name][$key] 
        : $details['class'][DETAIL_ANNOTATIONS][$name]
      );
    }

    function hasAnnotations() {
      $details= XPClass::detailsForClass($this->name);
      return $details ? !empty($details['class'][DETAIL_ANNOTATIONS]) : FALSE;
    }

    function getAnnotations() {
      $details= XPClass::detailsForClass($this->name);
      return $details ? $details['class'][DETAIL_ANNOTATIONS] : array();
    }
    
    function detailsForClass($class) {
      static $details= array();
      if (!$class) return NULL;        // Border case
      if (isset($details[$class])) return $details[$class];
      $details[$class]= array(array(), array());
      $name= strtr($class, '.', DIRECTORY_SEPARATOR);
      $l= strlen($name);
      foreach (get_included_files() as $file) {
        if ($name != substr($file, -10- $l, -10)) continue;
        // Found the class, now get API documentation
        $annotations= array();
        $comment= NULL;
        $members= TRUE;
        $tokens= token_get_all(file_get_contents($file));
        for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
          switch ($tokens[$i][0]) {
            case T_COMMENT:
              // Apidoc comment
              if (strncmp('/**', $tokens[$i][1], 3) == 0) {
                $comment= $tokens[$i][1];
                break;
              }
              // Annotations
              if (strncmp('#[@', $tokens[$i][1], 3) == 0) {
                $annotations[0]= substr($tokens[$i][1], 2);
              } elseif (strncmp('#', $tokens[$i][1], 1) == 0) {
                $annotations[0].= substr($tokens[$i][1], 1);
              }
              // End of annotations
              if (']' == substr(rtrim($tokens[$i][1]), -1)) {
                $annotations= eval('return array('.preg_replace(
                  array('/@([a-z_]+),/i', '/@([a-z_]+)\(\'([^\']+)\'\)/i', '/@([a-z_]+)\(/i', '/([a-z_]+) *= */i'),
                  array('\'$1\' => NULL,', '\'$1\' => \'$2\'', '\'$1\' => array(', '\'$1\' => '),
                  trim($annotations[0], "[]# \t\n\r").','
                ).');');
              }
              break;
            case T_CLASS:
              $details[$class]['class']= array(
                DETAIL_COMMENT      => $comment,
                DETAIL_ANNOTATIONS  => $annotations
              );
              $annotations= array();
              $comment= NULL;
              break;
            
            case T_VARIABLE:
              if (!$members) break;
              
              // Have a member variable
              $name= substr($tokens[$i][1], 1);
              $details[$class][0][$name]= array(
                DETAIL_ANNOTATIONS => $annotations
              );
              $annotations= array();
              break;
            
            case T_FUNCTION:
              $members= FALSE;
              while (T_STRING !== $tokens[$i][0]) $i++;
              $m= strtolower($tokens[$i][1]);
              $details[$class][1][$m]= array(
                DETAIL_MODIFIERS    => 0,
                DETAIL_ARGUMENTS    => array(),
                DETAIL_RETURNS      => 'void',
                DETAIL_THROWS       => array(),
                DETAIL_COMMENT      => preg_replace('/\n     \* ?/', "\n", "\n".substr(
                  $comment, 
                  4,                              // "/**\n"
                  strpos($comment, '* @')- 2      // position of first details token
                )),
                DETAIL_ANNOTATIONS  => $annotations,
                DETAIL_NAME         => $tokens[$i][1]
              );
              $matches= NULL;
              preg_match_all(
                '/@([a-z]+)\s*([^\r\n ]+) ?([^\r\n ]+)? ?(default ([^\r\n ]+))?/', 
                $comment, 
                $matches, 
                PREG_SET_ORDER
              );
              $annotations= array();
              $comment= NULL;
              foreach ($matches as $match) {
                switch ($match[1]) {
                  case 'access':
                  case 'model':
                    $details[$class][1][$m][DETAIL_MODIFIERS] |= constant('MODIFIER_'.strtoupper($match[2]));
                    break;
                  case 'param':
                    $details[$class][1][$m][DETAIL_ARGUMENTS][]= &new Argument(
                      isset($match[3]) ? $match[3] : 'param',
                      $match[2],
                      isset($match[4]),
                      isset($match[4]) ? $match[5] : NULL
                    );
                    break;
                  case 'return':
                    $details[$class][1][$m][DETAIL_RETURNS]= $match[2];
                    break;
                  case 'throws': 
                    $details[$class][1][$m][DETAIL_THROWS][]= $match[2];
                    break;
                }
              }
              break;
            default:
              // Empty
          }
        }
        // Break out of search loop
        break;
      }
      
      // Return details for specified class
      return $details[$class]; 
    }

    function detailsForMethod($class, $method) {
      $method= strtolower($method);
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[1][$method])) return $details[1][$method];
        $class= get_parent_class($class);
      }
      return NULL;
    }

    function detailsForField($class, $field) {
      $field= strtolower($field);
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[0][$field])) return $details[0][$field];
        $class= get_parent_class($class);
      }
      return NULL;
    }
    
    function &forName($name, $classloader= NULL) {
      if (NULL === $classloader) {
        $classloader= &ClassLoader::getDefault();
      }
    
      return $classloader->loadClass($name);
    }
    
    function &getClasses() {
      $ret= array();
      foreach (get_declared_classes() as $name) {
        if (xp::registry('class.'.$name)) $ret[]= &new XPClass($name);
      }
      return $ret;
    }
  }
  xp::registry('class.xpclass', 'lang.XPClass');
  // }}}
  // {{{ lang.reflect.Routine
  class Routine extends Object {
    var
      $_ref = NULL,
      $name = '';
    
    function __construct(&$ref, $name) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= strtolower($name);
    }

    function getName($asDeclared= FALSE) {
      if (!$asDeclared) return $this->name;
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_NAME];
    }
    
    
    function getModifiers() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_MODIFIERS];
    }
    
    function getModifierNames() {
      $m= $this->getModifiers();
      $names= array();
      if ($m & MODIFIER_ABSTRACT) $names[]= 'abstract';
      if ($m & MODIFIER_FINAL) $names[]= 'final';
      switch ($m & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
        case MODIFIER_PRIVATE: $names[]= 'private'; break;
        case MODIFIER_PROTECTED: $names[]= 'protected'; break;
        case MODIFIER_PUBLIC:
        default: $names[]= 'public'; break;
      }
      if ($m & MODIFIER_STATIC) $names[]= 'static';
      return $names;
    }
    
    function getArguments() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_ARGUMENTS];
    }

    function &getArgument($pos) {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      if (!isset($details[DETAIL_ARGUMENTS][$pos])) return NULL;
      return $details[DETAIL_ARGUMENTS][$pos];
    }

    function numArguments() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return sizeof($details[DETAIL_ARGUMENTS]);
    }

    function getReturnType() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return ltrim($details[DETAIL_RETURNS], '&');
    }

    function returnsReference() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return '&' == $details[DETAIL_RETURNS]{0};
    }
    
    function getExceptionNames() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_THROWS];
    }

    function getExceptionTypes() {
      $r= array();
      foreach ($this->getExceptionNames() as $name) {
        $r[]= &new XPClass($name);
      }
      return $r;
    }
    
    function &getDeclaringClass() {
      $class= $this->_ref;
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[1][$this->name])) return new XPClass($class);
        $class= get_parent_class($class);
      }
      return xp::null();
    }
    
    function getComment() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_COMMENT];
    }
    
    function hasAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);
      return $details && ($key 
        ? array_key_exists($key, @$details[DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details[DETAIL_ANNOTATIONS])
      );
    }

    function getAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);
      if (!$details || !($key 
        ? array_key_exists($key, @$details[DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details[DETAIL_ANNOTATIONS])
      )) return raise(
        'lang.ElementNotFoundException', 
        'Annotation "'.$name.($key ? '.'.$key : '').'" does not exist'
      );
      return ($key 
        ? $details[DETAIL_ANNOTATIONS][$name][$key] 
        : $details[DETAIL_ANNOTATIONS][$name]
      );
    }

    function hasAnnotations() {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);
      return $details ? !empty($details[DETAIL_ANNOTATIONS]) : FALSE;
    }

    function getAnnotations() {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);
      return $details ? $details[DETAIL_ANNOTATIONS] : array();
    }
    
    function toString() {
      $args= '';
      for ($arguments= $this->getArguments(), $i= 0, $s= sizeof($arguments); $i < $s; $i++) {
        if ($arguments[$i]->isOptional()) {
          $args.= ', ['.$arguments[$i]->getType().' $'.$arguments[$i]->getName().'= '.$arguments[$i]->getDefault().']';
        } else {
          $args.= ', '.$arguments[$i]->getType().' $'.$arguments[$i]->getName();
        }
      }
      if ($exceptions= $this->getExceptionNames()) {
        $throws= ' throws '.implode(', ', $exceptions);
      } else {
        $throws= '';
      }
      return sprintf(
        '%s %s %s(%s)%s',
        implode(' ', $this->getModifierNames()),
        $this->getReturnType(),
        $this->getName(),
        substr($args, 2),
        $throws
      );
    }
  }
  xp::registry('class.routine', 'lang.reflect.Routine');
  // }}}
  // {{{ lang.reflect.Argument
  class Argument extends Object {
    var
      $name     = '',
      $type     = '',
      $optional = FALSE,
      $default  = NULL;
    
    function __construct($name, $type= 'mixed', $optional= FALSE, $default= NULL) {
      $this->name= $name;
      $this->type= $type;
      $this->optional= $optional;
      $this->default= $default;
    }

    function getName() {
      return $this->name;
    }

    function getType() {
      return ltrim($this->type, '&');
    }

    function isPassedByReference() {
      return '&' == $this->type{0};
    }

    function isOptional() {
      return $this->optional;
    }

    function getDefault() {
      return $this->optional ? $this->default : FALSE;
    }
  }
  xp::registry('class.argument', 'lang.reflect.Argument');
  // }}}
  // {{{ lang.reflect.Method
  class Method extends Routine {
    
    function &invoke(&$obj, $args) {
      if (is_null($obj)) {
        return call_user_func_array(array($this->_ref, $this->name), $args);
      }
      
      if (!is(xp::nameOf($this->_ref), $obj)) {
        return throw(new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_ref),
          xp::nameOf($obj)
        )));
      }
      
      return call_user_func_array(array(&$obj, $this->name), $args);
    }
  }
  xp::registry('class.method', 'lang.reflect.Method');
  // }}}
  // {{{ lang.reflect.Field
  class Field extends Object {
    var
      $_ref   = NULL,
      $name   = '',
      $type   = NULL;
    
    function __construct(&$ref, $name, $type= NULL) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= $name;
      $this->type= $type;
    }

    function getName() {
      return $this->name;
    }
    
    function getType() {
      if (isset($this->type)) return $this->type;
      if ($details= XPClass::detailsForField($this->_ref, $this->name)) {
        if (isset($details[DETAIL_ANNOTATIONS]['type'])) return $details[DETAIL_ANNOTATIONS]['type'];
      }
      return NULL;
    }
    
    function &getDeclaringClass() {
      $class= $this->_ref;
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[0][$this->name])) return new XPClass($class);
        $class= get_parent_class($class);
      }
      return xp::null();
    }
    
    function &get(&$instance) {
      if (!is(xp::nameOf($this->_ref), $instance)) {
        return throw(new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_ref),
          xp::nameOf($instance)
        )));
      }
      return $instance->{$this->name};
    }
  }
  xp::registry('class.field', 'lang.reflect.Field');
  // }}}
  // {{{ lang.reflect.Constructor
  class Constructor extends Routine {
    
    function __construct(&$ref) {
      parent::__construct($ref, '__construct');
    }
    
    function &newInstance() {
      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      return eval('return new '.$this->_ref.'('.substr($paramstr, 2).');');
    }

    function getReturnType() {
      return xp::nameOf($this->_ref);
    }
  }
  xp::registry('class.constructor', 'lang.reflect.Constructor');
  // }}}
  // {{{ lang.NullPointerException
  class NullPointerException extends Exception {
  
  }
  xp::registry('class.nullpointerexception', 'lang.NullPointerException');
  // }}}
  // {{{ lang.IllegalAccessException
  class IllegalAccessException extends Exception {
  }
  xp::registry('class.illegalaccessexception', 'lang.IllegalAccessException');
  // }}}
  // {{{ lang.IllegalArgumentException
  class IllegalArgumentException extends Exception {
  }
  xp::registry('class.illegalargumentexception', 'lang.IllegalArgumentException');
  // }}}
  // {{{ lang.IllegalStateException
  class IllegalStateException extends Exception {
  
  }
  xp::registry('class.illegalstateexception', 'lang.IllegalStateException');
  // }}}
  // {{{ lang.FormatException
  class FormatException extends Exception {
  
  }
  xp::registry('class.formatexception', 'lang.FormatException');
  // }}}
  // {{{ lang.ClassLoader
  class ClassLoader extends Object {
    var 
      $classpath= '';
    
    function __construct($path= '') {
      if (!empty($path)) $this->classpath= $path.'.';
    }
    
    function &getDefault() {
      static $instance= NULL;
      
      if (!$instance) $instance= new ClassLoader();
      return $instance;
    }
    
    function findClass($class) {
      if (!$class) return FALSE;    // Border case
      $filename= str_replace('.', DIRECTORY_SEPARATOR, $this->classpath.$class).'.class.php';
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return realpath($dir.DIRECTORY_SEPARATOR.$filename);
      }
      return FALSE;
    }
    
    function &loadClass($class) {
      $name= xp::reflect($class);
      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        if (FALSE === include(strtr($qname, '.', DIRECTORY_SEPARATOR).'.class.php')) {
          return throw(new ClassNotFoundException('Class "'.$qname.'" not found'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }
      return new XPClass($name);
    }

    function &defineClass($class, $bytes) {
      $name= xp::reflect($class);
      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        if (FALSE === eval($bytes)) {
          return throw(new FormatException('Cannot define class "'.$qname.'"'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }      
      return new XPClass($name);
    }
  }
  xp::registry('class.classloader', 'lang.ClassLoader');
  // }}}
  // {{{ lang.ClassNotFoundException
  class ClassNotFoundException extends Exception {
  
  }
  xp::registry('class.classnotfoundexception', 'lang.ClassNotFoundException');
  // }}}
?>
