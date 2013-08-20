<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'lang.IClassLoader',
    'lang.FileSystemClassLoader',
    'lang.DynamicClassLoader',
    'lang.archive.ArchiveClassLoader'
  );
  
  /** 
   * Entry point class to loading classes, packages and resources.
   * Keeps a list of class loaders that load classes from the file
   * system, xar archives, memory, or various other places. These
   * loaders are asked for each class loading request, be it via
   * XPClass::forName(), uses(), requests from the Package class,
   * or explicit calls to ClassLoader::getDefault()->loadClass().
   *
   * Given the following code
   * <code>
   *   $class= ClassLoader::getDefault()->loadClass($name);
   * </code>
   * ...and the following include_path setting:
   * <pre>
   *   ".:/usr/local/lib/xp/xp-rt-5.4.0.xar:/home/classes/"
   * </pre>
   * ...the classloader will ask the class loader delegates:
   * <pre>
   * - FileSystemClassLoader(.)
   * - ArchiveClassLoader(/usr/local/lib/xp/xp-rt-5.4.0.xar)
   * - FileSystemClassLoader(/home/classes/)
   * </pre>
   * ...in the stated order. The first delegate to provide the class 
   * will be asked to load it. In case none of the delegates are able
   * to provide the class, a ClassNotFoundException will be thrown.
   * 
   * @test     xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @test     xp://net.xp_framework.unittest.reflection.ClassPathTest
   * @test     xp://net.xp_framework.unittest.reflection.ResourcesTest
   * @test     xp://net.xp_framework.unittest.reflection.PackageTest
   * @test     xp://net.xp_framework.unittest.reflection.RuntimeClassDefinitionTest
   * @test     xp://net.xp_framework.unittest.reflection.FullyQualifiedTest
   * @see      xp://lang.XPClass#forName
   * @see      xp://lang.reflect.Package#loadClass
   * @purpose  Class loading
   */
  final class ClassLoader extends Object implements IClassLoader {
    protected static
      $delegates  = array();

    static function __static() {
      xp::$loader= new self();
      
      // Scan include-path, setting up classloaders for each element
      foreach (xp::$classpath as $element) {
        if ('' === $element) {
          continue;
        } else if ('!' === $element{0}) {
          $before  = TRUE;
          $element = substr($element, 1);
        } else {
          $before= FALSE;
        }

        $resolved= realpath($element);
        if (is_dir($resolved)) {
          $cl= FileSystemClassLoader::instanceFor($resolved, FALSE);
        } else if (is_file($resolved)) {
          $cl= ArchiveClassLoader::instanceFor($resolved, FALSE);
        } else {
          if ('/' !== $element{0} && ':' !== $element{1}) {   // If not fully qualified
            $element.= ' (in '.getcwd().')';
          }
          xp::error('[bootstrap] Classpath element ['.$element.'] not found');
        }
        isset(self::$delegates[$cl->instanceId()]) || self::registerLoader($cl, $before);
      }
    }
    
    /**
     * Retrieve the default class loader
     *
     * @return  lang.ClassLoader
     */
    public static function getDefault() {
      return xp::$loader;
    }

    /**
     * Register a class loader from a path
     *
     * @param   string element
     * @param   bool before default FALSE whether to register this as the first loader,
     *          NULL wheather to figure out position by inspecting $element
     * @return  lang.IClassLoader the registered loader
     * @throws  lang.ElementNotFoundException if the path cannot be found
     */
    public static function registerPath($element, $before= FALSE) {
      if (NULL === $before && '!' === $element{0}) {
        $before  = TRUE;
        $element = substr($element, 1);
      } else {
        $before= (bool)$before;
      }
      if (is_dir($element)) {
        return self::registerLoader(FileSystemClassLoader::instanceFor($element), $before);
      } else if (is_file($element)) {
        return self::registerLoader(ArchiveClassLoader::instanceFor($element), $before);
      }
      raise('lang.ElementNotFoundException', 'Element "'.$element.'" not found');
    }
    
    /**
     * Register a class loader as a delegate
     *
     * @param   lang.IClassLoader l
     * @param   bool before default FALSE whether to register this as the first loader
     * @return  lang.IClassLoader the registered loader
     */
    public static function registerLoader(IClassLoader $l, $before= FALSE) {
      if ($before) {
        self::$delegates= array_merge(array($l->instanceId() => $l), self::$delegates);
      } else {
        self::$delegates[$l->instanceId()]= $l;
      }
      return $l;
    }

    /**
     * Unregister a class loader as a delegate
     *
     * @param   lang.IClassLoader l
     * @return  bool TRUE if the delegate was unregistered
     */
    public static function removeLoader(IClassLoader $l) {
      $id= $l->instanceId();
      if (!isset(self::$delegates[$id])) return FALSE;
      unset(self::$delegates[$id]);
      return TRUE;
    }

    /**
     * Get class loader delegates
     *
     * @return  lang.IClassLoader[]
     */
    public static function getLoaders() {
      return array_values(self::$delegates);
    }

    /**
     * Helper method to turn a given value into a class object
     *
     * @param  var class
     * @return lang.XPClass
     */
    protected static function classOf($class) {
      if ($class instanceof XPClass) {
        return $class;
      } else {
        return XPClass::forName(strstr($class, '.') ? $class : xp::nameOf($class));
      }
    }

    /**
     * Define a class with a given name
     *
     * @param   string class fully qualified class name
     * @param   var parent The parent class either by qualified name or XPClass instance
     * @param   var[] interfaces The implemented interfaces either by qualified names or XPClass instances
     * @param   string bytes default "{}" inner sourcecode of class (containing {}) 
     * @return  lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     */
    public static function defineClass($class, $parent, $interfaces, $bytes= '{}') {
      $name= xp::reflect($class);
      if (!isset(xp::$cl[$class])) {

        // Load parent class and implemented interfaces
        $super= self::classOf($parent)->literal();
        $if= array();
        foreach ((array)$interfaces as $interface) {
          $if[]= self::classOf($interface)->literal();
        }

        // Define class
        with ($dyn= self::registerLoader(DynamicClassLoader::instanceFor(__METHOD__))); {
          $dyn->setClassBytes($class, sprintf(
            'class %s extends %s%s %s',
            $name,
            $super,
            $interfaces ? ' implements '.implode(', ', $if) : '',
            $bytes
          ));
          
          return $dyn->loadClass($class);
        }
      }
      
      return new XPClass($name);
    }
    
    /**
     * Define an interface with a given name
     *
     * @param   string class fully qualified class name
     * @param   var[] parents The parent interfaces either by qualified names or XPClass instances
     * @param   string bytes default "{}" inner sourcecode of class (containing {}) 
     * @return  lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     */
    public static function defineInterface($class, $parents, $bytes= '{}') {
      $name= xp::reflect($class);
      if (!isset(xp::$cl[$class])) {

        // Load parent class and implemented interfaces
        $if= array();
        foreach ((array)$parents as $interface) {
          $if[]= self::classOf($interface)->literal();
        }

        // Define class
        with ($dyn= self::registerLoader(DynamicClassLoader::instanceFor(__METHOD__))); {
          $dyn->setClassBytes($class, sprintf(
            'interface %s%s %s',
            $name,
            $parents ? ' extends '.implode(', ', $if) : '',
            $bytes
          ));
          
          return $dyn->loadClass($class);
        }
      }
      
      return new XPClass($name);
    }

    /**
     * Loads a class
     *
     * @param   string class fully qualified class name
     * @return  string class name of class loaded
     * @throws  lang.ClassNotFoundException in case the class can not be found
     * @throws  lang.ClassFormatException in case the class format is invalud
     */
    public function loadClass0($class) {
      if (isset(xp::$cl[$class])) return xp::reflect($class);
      
      // Ask delegates
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return $delegate->loadClass0($class);
      }
      throw new ClassNotFoundException($class, self::getLoaders());
    }

    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return TRUE;
      }
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested URI as a class
     *
     * @param   string uri
     * @return  bool
     */
    public function providesUri($uri) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesUri($uri)) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesResource($filename)) return TRUE;
      }
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesPackage($package)) return TRUE;
      }
      return FALSE;
    }

    /**
     * Find the class by the specified name
     *
     * @param   string class fully qualified class name
     * @return  lang.IClassLoader the classloader that provides this class
     */
    public function findClass($class) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return $delegate;
      }
      return xp::null();
    }

    /**
     * Find the class by the specified URI
     *
     * @param   string uri
     * @return  lang.IClassLoader the classloader that provides this uri
     */
    public function findUri($uri) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesUri($uri)) return $delegate;
      }
      return xp::null();
    }

    /**
     * Find the package by the specified name
     *
     * @param   string package fully qualified package name
     * @return  lang.IClassLoader the classloader that provides this class
     */
    public function findPackage($package) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesPackage($package)) return $delegate;
      }
      return xp::null();
    }    
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->loadClass0($class));
    }    

    /**
     * Find the resource by the specified name
     *
     * @param   string name resource name
     * @return  lang.IClassLoader the classloader that provides this resource
     */
    public function findResource($name) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesResource($name)) return $delegate;
      }
      return xp::null();
    }    

    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesResource($string)) return $delegate->getResource($string);
      }
      raise('lang.ElementNotFoundException', sprintf(
        'No classloader provides resource "%s" {%s}',
        $string,
        xp::stringOf(self::getLoaders())
      ));
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesResource($string)) return $delegate->getResourceAsStream($string);
      }
      raise('lang.ElementNotFoundException', sprintf(
        'No classloader provides resource "%s" {%s}',
        $string,
        xp::stringOf(self::getLoaders())
      ));
    }

    /**
     * Find the class by a given URI
     *
     * @param   string uri
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadUri($uri) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesUri($uri)) return $delegate->loadUri($uri);
      }
      throw new ClassNotFoundException('URI:'.$uri, self::getLoaders());
    }

    /**
     * Get package contents
     *
     * @param   string package
     * @return  string[] filenames
     */
    public function packageContents($package) {
      $contents= array();
      foreach (self::$delegates as $delegate) {
        $contents= array_merge($contents, $delegate->packageContents($package));
      }
      return array_unique($contents);
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName();
    }

    /**
     * Returns a unique identifier for this class loader instance
     *
     * @return  string
     */
    public function instanceId() {
      return '*';
    }
  }
?>
