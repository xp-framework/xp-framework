<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassNotFoundException');
  
  /** 
   * Loads a class
   * 
   * @purpose  Load classes
   * @test     xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @see      xp://lang.XPClass#forName
   */
  class ClassLoader extends Object {
    var 
      $classpath= '';
    
    /**
     * Constructor. 
     *
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name)
     *
     * @access  public
     * @param   string path default '' classpath
     */
    function __construct($path= '') {
      if (!empty($path)) $this->classpath= $path.'.';
    }

    /**
     * Load class bytes
     *
     * @access  public
     * @param   string name fully qualified class name
     * @return  string
     */
    function loadClassBytes($name) {
      return file_get_contents($this->findClass($name));
    }

    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return (
        $this->getClassName().
        ($this->classpath ? '<'.rtrim($this->classpath, '.').'>' : '').
        '(search= '.xp::stringOf(explode(PATH_SEPARATOR, ini_get('include_path'))).')'
      );
    }

    /**
     * Retrieve details for a specified class.
     *
     * @access  public
     * @param   string class fully qualified class name
     * @return  array or NULL to indicate no details are available
     */
    function getClassDetails($class) {
      if (!($bytes= $this->loadClassBytes($class))) return NULL;

      // Found the class, now get API documentation
      $details= array(array(), array());
      $annotations= array();
      $comment= NULL;
      $members= TRUE;

      $tokens= token_get_all($bytes);
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
                array('/@([a-z_]+),/i', '/@([a-z_]+)\(\'([^\']+)\'\)/i', '/@([a-z_]+)\(/i', '/([^a-z_@])([a-z_]+) *= */i'),
                array('\'$1\' => NULL,', '\'$1\' => \'$2\'', '\'$1\' => array(', '$1\'$2\' => '),
                trim($annotations[0], "[]# \t\n\r").','
              ).');');
            }
            break;

          case T_CLASS:
            $details['class']= array(
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
            $details[0][$name]= array(
              DETAIL_ANNOTATIONS => $annotations
            );
            $annotations= array();
            break;

          case T_FUNCTION:
            $members= FALSE;
            while (T_STRING !== $tokens[$i][0]) $i++;
            $m= strtolower($tokens[$i][1]);
            $details[1][$m]= array(
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
              '/@([a-z]+)\s*([^<\r\n]+<[^>]+>|[^\r\n ]+) ?([^\r\n ]+)? ?(default ([^\r\n ]+))?/',
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
                  $details[1][$m][DETAIL_MODIFIERS] |= constant('MODIFIER_'.strtoupper($match[2]));
                  break;

                case 'param':
                  $details[1][$m][DETAIL_ARGUMENTS][]= array(
                    isset($match[3]) ? $match[3] : 'param',
                    $match[2],
                    isset($match[4]),
                    isset($match[4]) ? $match[5] : NULL
                  );
                  break;

                case 'return':
                  $details[1][$m][DETAIL_RETURNS]= $match[2];
                  break;

                case 'throws': 
                  $details[1][$m][DETAIL_THROWS][]= $match[2];
                  break;
              }
            }
            break;

          default:
            // Empty
        }
      }
      
      return $details; 
    }
    
    /**
     * Retrieve the default class loader
     *
     * @model   static
     * @access  public
     * @return  &lang.ClassLoader
     */
    function &getDefault() {
      static $instance= NULL;
      
      if (!$instance) $instance= new ClassLoader();
      return $instance;
    }
    
    /**
     * Find a class by the specified name (but do not load it)
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  string filename, FALSE if not found
     */
    function findClass($class) {
      if (!$class) return FALSE;    // Border case

      $filename= str_replace('.', DIRECTORY_SEPARATOR, $this->classpath.$class).'.class.php';
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return realpath($dir.DIRECTORY_SEPARATOR.$filename);
      }
      return FALSE;
    }
    
    /**
     * Load the class by the specified name
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  &lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
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

      $c= &new XPClass($name);
      return $c;
    }

    /**
     * Define a class with a given name
     *
     * @access  protected
     * @param   string class fully qualified class name
     * @param   string bytes sourcecode of the class
     * @return  &lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     */
    function &_defineClassFromBytes($class, $bytes) {
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        if (FALSE === eval($bytes)) {
          return throw(new FormatException('Cannot define class "'.$qname.'"'));
        }
        if (!class_exists($name)) {
          return throw(new FormatException('Class "'.$qname.'" not defined'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }      

      $c= &new XPClass($name);
      return $c;
    }
    
    /**
     * Define a class with a given name
     *
     * @access  public
     * @param   string class fully qualified class name
     * @param   string parent either sourcecode of the class or FQCN of parent
     * @param   string[] interfaces default NULL FQCNs of implemented interfaces
     * @param   string bytes default NULL inner sourcecode of class (containing {}) 
     * @return  &lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     * @throws  lang.ClassNotFoundException if given parent class does not exist
     */
    function &defineClass($class, $parent, $interfaces= NULL, $bytes= NULL) {
      
      // If invoked with less than four arguments, old behaviour will be executed
      if (NULL === $bytes) {
        return $this->_defineClassFromBytes($class, $parent);
      }
      
      $name= xp::reflect($class);
      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        $parentName= xp::reflect($parent);
        
        if (!class_exists($parentName)) {
          return throw(new ClassNotFoundException('Parent class '.$parent.' does not exist.'));
        }
        
        $newBytes= 'class '.$name.' extends '.$parentName.' '.$bytes;
        if (FALSE === eval($newBytes)) {
          return throw(new FormatException('Cannot define class "'.$qname.'"'));
        }
        
        if (!class_exists($name)) {
          return throw(new FormatException('Class "'.$qname.'" not defined'));
        }
        
        xp::registry('class.'.$name, $qname);
        if (sizeof($interfaces)) { xp::implements($name, $interfaces); }
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }
      
      $c= &new XPClass($name);
      return $c;
    }
    
    /**
     * Loads a resource.
     *
     * @access  public
     * @param   string filename name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    function getResource($filename) {
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return file_get_contents($dir.DIRECTORY_SEPARATOR.$filename);
      }
    
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @access  public
     * @param   string filename name of resource
     * @return  &io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    function &getResourceAsStream($filename) {
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return new File($filename);
      }
    
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }
  }
?>
