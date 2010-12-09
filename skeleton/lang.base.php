<?php
/* This file provides the core for the XP framework
 * 
 * $Id$
 */

  // {{{ final class xp
  final class xp {
    const CLASS_FILE_EXT= '.class.php';

    public static $registry  = array(
      'errors'     => array(),
      'sapi'       => array(),
      'class.xp'   => '<xp>',
      'class.null' => '<null>',
      'cl.level'   => 0,
      'cl.inv'     => array()
    );
    
    // {{{ public string loadClass0(string name)
    //     Loads a class by its fully qualified name
    function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) {
        return substr(array_search($class, xp::$registry, TRUE), 6);
      }

      foreach (xp::$registry['classpath'] as $path) {

        // If path is a directory and the included file exists, load it
        if (is_dir($path) && file_exists($f= $path.DIRECTORY_SEPARATOR.strtr($class, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT)) {
          $cl= 'FileSystemClassLoader';
        } else if (is_file($path) && file_exists($f= 'xar://'.$path.'?'.strtr($class, '.', '/').xp::CLASS_FILE_EXT)) {
          $cl= 'ArchiveClassLoader';
        } else {
          continue;
        }

        // Load class        
        $package= NULL;
        xp::$registry['classloader.'.$class]= $cl.'://'.$path;
        xp::$registry['cl.level']++;
        $r= include($f);
        xp::$registry['cl.level']--;
        if (FALSE === $r) {
          unset(xp::$registry['classloader.'.$class]);
          continue;
        }

        // Register class name and call static initializer if available
        $name= ($package ? strtr($package, '.', '·').'·' : '').substr($class, (FALSE === ($p= strrpos($class, '.')) ? 0 : $p + 1));
        xp::$registry['class.'.$name]= $class;
        method_exists($name, '__static') && xp::$registry['cl.inv'][]= array($name, '__static');
        if (0 == xp::$registry['cl.level']) {
          $invocations= xp::$registry['cl.inv'];
          xp::$registry['cl.inv']= array();
          foreach ($invocations as $inv) call_user_func($inv);
        }

        return $name;
      }
      
      xp::error('Cannot bootstrap class '.$class.' (include_path= '.get_include_path().')');
    }
    // }}}

    // {{{ public string nameOf(string name)
    //     Returns the fully qualified name
    static function nameOf($name) {
      $k= 'class.'.$name;
      return isset(xp::$registry[$k]) ? xp::$registry[$k] : 'php.'.$name;
    }
    // }}}

    // {{{ public string typeOf(var arg)
    //     Returns the fully qualified type name
    static function typeOf($arg) {
      return is_object($arg) ? xp::nameOf(get_class($arg)) : gettype($arg);
    }
    // }}}

    // {{{ public string stringOf(var arg [, string indent default ''])
    //     Returns a string representation of the given argument
    static function stringOf($arg, $indent= '') {
      static $protect= array();
      
      if (is_string($arg)) {
        return '"'.$arg.'"';
      } else if (is_bool($arg)) {
        return $arg ? 'true' : 'false';
      } else if (is_null($arg)) {
        return 'null';
      } else if ($arg instanceof null) {
        return '<null>';
      } else if (is_int($arg) || is_float($arg)) {
        return (string)$arg;
      } else if ($arg instanceof Generic && !isset($protect[(string)$arg->hashCode()])) {
        $protect[(string)$arg->hashCode()]= TRUE;
        $s= $arg->toString();
        unset($protect[(string)$arg->hashCode()]);
        return $s;
      } else if (is_array($arg)) {
        $ser= serialize($arg);
        if (isset($protect[$ser])) return '->{:recursion:}';
        $protect[$ser]= TRUE;
        $r= "[\n";
        foreach (array_keys($arg) as $key) {
          $r.= $indent.'  '.$key.' => '.xp::stringOf($arg[$key], $indent.'  ')."\n";
        }
        unset($protect[$ser]);
        return $r.$indent.']';
      } else if (is_object($arg)) {
        $ser= serialize($arg);
        if (isset($protect[$ser])) return '->{:recursion:}';
        $protect[$ser]= TRUE;
        $r= xp::nameOf(get_class($arg))." {\n";
        $vars= (array)$arg;
        foreach (array_keys($vars) as $key) {
          $r.= $indent.'  '.$key.' => '.xp::stringOf($vars[$key], $indent.'  ')."\n";
        }
        unset($protect[$ser]);
        return $r.$indent.'}';
      } else if (is_resource($arg)) {
        return 'resource(type= '.get_resource_type($arg).', id= '.(int)$arg.')';
      }
    }
    // }}}

    // {{{ public static void extensions(string class, string scope)
    //     Registers extension methods for a certain scope
    static function extensions($class, $scope) {
      foreach (create(new XPClass($class))->getMethods() as $method) {
        if (MODIFIER_STATIC & $method->getModifiers() && $method->numParameters() > 0) {
          $param= $method->getParameter(0);
          if ('self' === $param->getName()) {
            self::$registry['ext'][$scope][xp::reflect($param->getTypeName())]= $class;
          }
        }
      }
    }
    // }}}

    // {{{ public void gc([string file default NULL])
    //     Runs the garbage collector
    static function gc($file= NULL) {
      if ($file) {
        unset(xp::$registry['errors'][$file]);
      } else {
        xp::$registry['errors']= array();
      }
    }
    // }}}

    // {{{ public <null> null()
    //     Runs a fatal-error safe version of NULL
    static function null() {
      return xp::$registry['null'];
    }
    // }}}

    // {{{ public bool errorAt(string file [, int line)
    //     Returns whether an error occured at the specified position
    static function errorAt($file, $line= -1) {
      $errors= xp::$registry['errors'];
      
      // If no line is given, check for an error in the file
      if ($line < 0) return !empty($errors[$file]);
      
      // Otherwise, check for an error in the file on a certain line
      return !empty($errors[$file][$line]);
    }
    // }}}
    
    // {{{ public var sapi(string* sapis)
    //     Sets an SAPI
    static function sapi() {
      foreach ($a= func_get_args() as $name) {
        foreach (xp::$registry['classpath'] as $path) {
          $filename= 'sapi'.DIRECTORY_SEPARATOR.strtr($name, '.', DIRECTORY_SEPARATOR).'.sapi.php';
          if (is_dir($path) && file_exists($f= $path.DIRECTORY_SEPARATOR.$filename)) {
            require_once($f);
            continue 2;
          } else if (is_file($path) && file_exists($f= 'xar://'.$path.'?'.strtr($filename, DIRECTORY_SEPARATOR, '/'))) {
            require_once($f);
            continue 2;
          }
        }
        
        xp::error('Cannot open SAPI '.$name.' (include_path='.get_include_path().')');
      }
      xp::$registry['sapi']= $a;
    }
    // }}}
    
    // {{{ internal var registry(var args*)
    //     Stores static data
    static function registry() {
      switch (func_num_args()) {
        case 0: return xp::$registry;
        case 1: return @xp::$registry[func_get_arg(0)];
        case 2: xp::$registry[func_get_arg(0)]= func_get_arg(1); break;
      }
      return NULL;
    }
    // }}}
    
    // {{{ internal string reflect(string type)
    //     Retrieve type literal for a given type name
    static function reflect($type) {
      if ('string' === $type || 'int' === $type || 'double' === $type || 'bool' == $type) {
        return 'þ'.$type;
      } else if ('var' === $type) {
        return $type;
      } else if ('[]' === substr($type, -2)) {
        return '¦'.xp::reflect(substr($type, 0, -2));
      } else if ('[:' === substr($type, 0, 2)) {
        return '»'.xp::reflect(substr($type, 2, -1));
      } else if (FALSE !== ($p= strpos($type, '<'))) {
        $l= xp::reflect(substr($type, 0, $p)).'··';
        for ($args= substr($type, $p+ 1, -1).',', $o= 0, $brackets= 0, $i= 0, $s= strlen($args); $i < $s; $i++) {
          if (',' === $args{$i} && 0 === $brackets) {
            $l.= xp::reflect(ltrim(substr($args, $o, $i- $o))).'¸';
            $o= $i+ 1;
          } else if ('<' === $args{$i}) {
            $brackets++;
          } else if ('>' === $args{$i}) {
            $brackets--;
          }
        }
        return substr($l, 0, -1);
      } else {      
        $l= array_search($type, xp::$registry, TRUE);
        return $l ? substr($l, 6) : substr($type, (FALSE === $p= strrpos($type, '.')) ? 0 : $p+ 1);
      }
    }
    // }}}

    // {{{ internal void error(string message)
    //     Throws a fatal error and exits with exitcode 61
    static function error($message) {
      restore_error_handler();
      trigger_error($message, E_USER_ERROR);
      exit(0x3d);
    }
  }
  // }}}

  // {{{ final class null
  class null {

    // {{{ public object __construct(void)
    //     Constructor to avoid magic __call invokation
    public function __construct() {
      if (isset(xp::$registry['null'])) {
        throw new IllegalAccessException('Cannot create new instances of xp::null()');
      }
    }
    
    // {{{ public void __clone(void)
    //     Clone interceptor
    public function __clone() {
      throw new NullPointerException('Object cloning intercepted.');
    }
    // }}}
    
    // {{{ magic var __call(string name, var[] args)
    //     Call proxy
    function __call($name, $args) {
      throw new NullPointerException('Method.invokation('.$name.')');
    }
    // }}}

    // {{{ magic void __set(string name, var value)
    //     Set proxy
    function __set($name, $value) {
      throw new NullPointerException('Property.write('.$name.')');
    }
    // }}}

    // {{{ magic var __get(string name)
    //     Set proxy
    function __get($name) {
      throw new NullPointerException('Property.read('.$name.')');
    }
    // }}}
  }
  // }}}
  // {{{ final class xploader
  class xarloader {
    public
      $position     = 0,
      $archive      = '',
      $filename     = '';
      
    // {{{ static var[] acquire(string archive)
    //     Archive instance handling pool function, opens an archive and reads header only once
    static function acquire($archive) {
      static $archives= array();
      static $unpack= array(
        1 => 'a80id/a80*filename/a80*path/V1size/V1offset/a*reserved',
        2 => 'a240id/V1size/V1offset/a*reserved'
      );
      
      if (!isset($archives[$archive])) {
        $archives[$archive]= array();
        $current= &$archives[$archive];
        $current['handle']= fopen($archive, 'rb');
        $header= unpack('a3id/c1version/V1indexsize/a*reserved', fread($current['handle'], 0x0100));
        if ('CCA' != $header['id']) raise('lang.FormatException', 'Malformed archive '.$archive);
        for ($current['index']= array(), $i= 0; $i < $header['indexsize']; $i++) {
          $entry= unpack(
            $unpack[$header['version']], 
            fread($current['handle'], 0x0100)
          );
          $current['index'][$entry['id']]= array($entry['size'], $entry['offset']);
        }
      }

      return $archives[$archive];
    }
    // }}}
    
    // {{{ function bool stream_open(string path, string mode, int options, string opened_path)
    //     Open the given stream and check if file exists
    function stream_open($path, $mode, $options, $opened_path) {
      sscanf($path, 'xar://%[^?]?%[^$]', $archive, $file);
      $this->archive= urldecode($archive);
      $this->filename= $file;
      
      $current= self::acquire($this->archive);
      return isset($current['index'][$this->filename]);
    }
    // }}}
    
    // {{{ string stream_read(int count)
    //     Read $count bytes up-to-length of file
    function stream_read($count) {
      $current= self::acquire($this->archive);
      if (!isset($current['index'][$this->filename])) return FALSE;
      if ($current['index'][$this->filename][0] == $this->position || 0 == $count) return FALSE;

      fseek($current['handle'], 0x0100 + sizeof($current['index']) * 0x0100 + $current['index'][$this->filename][1] + $this->position, SEEK_SET);
      $bytes= fread($current['handle'], min($current['index'][$this->filename][0]- $this->position, $count));
      $this->position+= strlen($bytes);
      return $bytes;
    }
    // }}}
    
    // {{{ bool stream_eof()
    //     Returns whether stream is at end of file
    function stream_eof() {
      $current= self::acquire($this->archive);
      return $this->position >= $current['index'][$this->filename][0];
    }
    // }}}
    
    // {{{ <string,int> stream_stat()
    //     Retrieve status of stream
    function stream_stat() {
      $current= self::acquire($this->archive);
      return array(
        'size'  => $current['index'][$this->filename][0]
      );
    }
    // }}}

    // {{{ bool stream_seek(int offset, int whence)
    //     Callback for fseek
    function stream_seek($offset, $whence) {
      switch ($whence) {
        case SEEK_SET: $this->position= $offset; break;
        case SEEK_CUR: $this->position+= $offset; break;
        case SEEK_END: 
          $current= self::acquire($this->archive);
          $this->position= $current['index'][$this->filename][0] + $offset; 
          break;
      }
      return TRUE;
    }
    // }}}
    
    // {{{ int stream_tell
    //     Callback for ftell
    function stream_tell() {
      return $this->position;
    }
    // }}}
    
    // {{{ <string,int> url_stat(string path)
    //     Retrieve status of url
    function url_stat($path) {
      sscanf($path, 'xar://%[^?]?%[^$]', $archive, $file);
      $current= self::acquire(urldecode($archive));

      return isset($current['index'][$file]) 
        ? array('size' => $current['index'][$file][0])
        : FALSE
      ;
    }
    // }}}
  }
  // }}}

  // {{{ internal void __error(int code, string msg, string file, int line)
  //     Error callback
  function __error($code, $msg, $file, $line) {
    if (0 == error_reporting() || is_null($file)) return;

    if (E_RECOVERABLE_ERROR == $code) {
      throw new IllegalArgumentException($msg.' @ '.$file.':'.$line);
    } else {
      $bt= debug_backtrace();
      $class= (isset($bt[1]['class']) ? $bt[1]['class'] : NULL);
      $method= (isset($bt[1]['function']) ? $bt[1]['function'] : NULL);
      
      if (!isset(xp::$registry['errors'][$file][$line][$msg])) {
        xp::$registry['errors'][$file][$line][$msg]= array(
          'class'   => $class,
          'method'  => $method,
          'cnt'     => 1
        );
      } else {
        xp::$registry['errors'][$file][$line][$msg]['cnt']++;
      }
    }
  }
  // }}}

  // {{{ void uses (string* args)
  //     Uses one or more classes
  function uses() {
    $scope= NULL;
    foreach (func_get_args() as $str) {
      $class= xp::$registry['loader']->loadClass0($str);
      if (method_exists($class, '__import')) {
        if (NULL === $scope) {
          $trace= debug_backtrace();
          $scope= xp::reflect($trace[2]['args'][0]);
        }
        call_user_func(array($class, '__import'), $scope);
      }
    }
  }
  // }}}

  // {{{ void raise (string classname, var* args)
  //     throws an exception by a given class name
  function raise($classname) {
    try {
      $class= XPClass::forName($classname);
    } catch (ClassNotFoundException $e) {
      xp::error($e->getMessage());
    }
    
    $a= func_get_args();
    throw call_user_func_array(array($class, 'newInstance'), array_slice($a, 1));
  }
  // }}}

  // {{{ void finally (void)
  //     Syntactic sugar. Intentionally empty
  function finally() {
  }
  // }}}

  // {{{ Generic cast (Generic expression, string type)
  //     Casts an expression.
  function cast(Generic $expression= NULL, $type) {
    if (NULL === $expression) {
      return xp::null();
    } else if (XPClass::forName($type)->isInstance($expression)) {
      return $expression;
    }

    raise('lang.ClassCastException', 'Cannot cast '.xp::typeOf($expression).' to '.$type);
   }

  // {{{ proto bool is(string type, var object)
  //     Checks whether a given object is an instance of the type given
  function is($type, $object) {
    if (NULL === $type) {
      return $object instanceof null;
    } else if ('int' === $type) {
      return is_int($object);
    } else if ('double' === $type) {
      return is_double($object);
    } else if ('string' === $type) {
      return is_string($object);
    } else if ('bool' === $type) {
      return is_bool($object);
    } else if ('var' === $type) {
      return TRUE;
    } else if ('[]' === substr($type, -2)) {
      $type= substr($type, 0, -2);
      foreach ($object as $element) {
        if (!is($type, $element)) return FALSE;
      }
      return TRUE;
    } else if ('[:' === substr($type, 0, 2)) {
      $type= substr($type, 2, -1);
      foreach ($object as $element) {
        if (!is($type, $element)) return FALSE;
      }
      return TRUE;
    } else {
      $type= xp::reflect($type);
      return $object instanceof $type;
    }
  }
  // }}}

  // {{{ proto void delete(&lang.Object object)
  //     Destroys an object
  function delete(&$object) {
    $object= NULL;
  }
  // }}}

  // {{{ proto void with(expr)
  //     Syntactic sugar. Intentionally empty
  function with() {
  }
  // }}}
  
  // {{{ proto deprecated var ref(var object)
  //     Creates a "reference" to an object
  function ref(&$object) {
    return array(&$object);
  }
  // }}}

  // {{{ proto deprecated &var deref(&mixed expr)
  //     Dereferences an expression
  function &deref(&$expr) {
    if (is_array($expr)) return $expr[0]; else return $expr;
  }
  // }}}

  // {{{ proto var this(var expr, var offset)
  //     Indexer access for a given expression
  function this($expr, $offset) {
    return $expr[$offset];
  }
  // }}}
  
  // {{{ proto lang.Object newinstance(string spec, var[] args, string bytes)
  //     Anonymous instance creation
  function newinstance($spec, $args, $bytes) {
    static $u= 0;

    // Check for an anonymous generic 
    if (strstr($spec, '<')) {
      $type= Type::forName($spec)->literal();
    } else {
      $type= xp::reflect(strstr($spec, '.') ? $spec : xp::nameOf($spec));
      if (!class_exists($type, FALSE) && !interface_exists($type, FALSE)) {
        xp::error(xp::stringOf(new Error('Class "'.$spec.'" does not exist')));
        // Bails
      }
    }

    $name= $type.'·'.(++$u);
    
    // Checks whether an interface or a class was given
    $cl= DynamicClassLoader::instanceFor(__FUNCTION__);
    if (interface_exists($type)) {
      $cl->setClassBytes($name, 'class '.$name.' extends Object implements '.$type.' '.$bytes);
    } else {
      $cl->setClassBytes($name, 'class '.$name.' extends '.$type.' '.$bytes);
    }

    $cl->loadClass0($name);

    // Build paramstr for evaluation
    for ($paramstr= '', $i= 0, $m= sizeof($args); $i < $m; $i++) {
      $paramstr.= ', $args['.$i.']';
    }
    return eval('return new '.$name.'('.substr($paramstr, 2).');');
  }
  // }}}

  // {{{ lang.Generic create(var spec)
  //     Creates a generic object
  function create($spec) {
    if ($spec instanceof Generic) return $spec;

    // Parse type specification: "new " TYPE "()"?
    // TYPE:= B "<" ARGS ">"
    // ARGS:= TYPE [ "," TYPE [ "," ... ]]
    $b= strpos($spec, '<');
    $base= substr($spec, 4, $b- 4);
    $typeargs= Type::forNames(substr($spec, $b+ 1, strrpos($spec, '>')- $b- 1));
    
    // BC check: For classes with __generic field, instanciate without 
    // invoking the constructor and pass type information. This is done 
    // so that the constructur can already use generic types.
    $class= XPClass::forName(strstr($base, '.') ? $base : xp::nameOf($base));
    if ($class->hasField('__generic')) {
      $__id= microtime();
      $name= xp::reflect($classname);
      $instance= unserialize('O:'.strlen($name).':"'.$name.'":1:{s:4:"__id";s:'.strlen($__id).':"'.$__id.'";}');
      foreach ($typeargs as $type) {
        $instance->__generic[]= xp::reflect($type->getName());
      }

      // Call constructor if available
      if (method_exists($instance, '__construct')) {
        $a= func_get_args();
        call_user_func_array(array($instance, '__construct'), array_slice($a, 1));
      }

      return $instance;
    }
    
    // BC: Wrap IllegalStateExceptions into IllegalArgumentExceptions
    try {
      $type= $class->newGenericType($typeargs);
    } catch (IllegalStateException $e) {
      throw new IllegalArgumentException($e->getMessage());
    }

    // Instantiate
    if ($type->hasConstructor()) {
      $args= func_get_args();
      try {
        return $type->getConstructor()->newInstance(array_slice($args, 1));
      } catch (TargetInvocationException $e) {
        throw $e->getCause();
      }
    } else {
      return $type->newInstance();
    }
  }
  // }}}

  // {{{ lang.Type typeof(mixed arg)
  //     Returns type
  function typeof($arg) {
    if ($arg instanceof Generic) {
      return $arg->getClass();
    } else if (NULL === $arg) {
      return Type::$VOID;
    } else if (is_array($arg)) {
      return 0 === key($arg) ? ArrayType::forName('var[]') : MapType::forName('[:var]');
    } else {
      return Primitive::forName(gettype($arg));
    }
  }
  // }}}

  // {{{ initialization
  error_reporting(E_ALL);
  
  // Constants
  define('LONG_MAX', PHP_INT_MAX);
  define('LONG_MIN', -PHP_INT_MAX - 1);

  // Hooks
  set_error_handler('__error');
  
  // Get rid of magic quotes 
  get_magic_quotes_gpc() && xp::error('[xp::core] magic_quotes_gpc enabled');
  date_default_timezone_set(ini_get('date.timezone')) || xp::error('[xp::core] date.timezone not configured properly.');
  ini_set('magic_quotes_runtime', FALSE);
  
  // Registry initialization
  xp::$registry['null']= new null();
  xp::$registry['loader']= new xp();
  xp::$registry['classpath']= explode(PATH_SEPARATOR, get_include_path());

  // Register stream wrapper for .xar class loading
  stream_wrapper_register('xar', 'xarloader');

  // Omnipresent classes
  uses(
    'lang.Object',
    'lang.Error',
    'lang.XPException',
    'lang.XPClass',
    'lang.NullPointerException',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'lang.FormatException',
    'lang.ClassLoader'
  );
  // }}}
?>
