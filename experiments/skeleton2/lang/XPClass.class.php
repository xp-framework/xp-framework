<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

namespace lang {
 
  /**
   * Represents classes. Every instance of an XP class has an method
   * called getClass() which returns an instance of this class.
   *
   * Warning:
   * This class cannot be instatiated with new, instead use either 
   * the $o->getClass() syntax on any object or the static method 
   * $class= XPClass::forName('fully.qualified.Name')
   *
   * To retrieve the fully qualified name of a class, use this:
   * <code>
   *   $o= new File();
   *   echo 'The class name for $o is '.$o->getClass()->getName();
   * </code>
   *
   * @see     xp://lang.Object#getClass()
   * @purpose Class reflection
   */
  class XPClass extends lang::Object {
    private
      $ref      = NULL,
      $name     = '';
    
    /**
     * Constructor
     *
     * @access  private
     * @param   string ref
     */
    private function __construct($ref) {
      $this->ref= $ref;
      $this->name= isset(xp::registry::$names[$ref]) ? xp::registry::$names[$ref] : 'php.'.$ref;
    }
    
    /**
     * Retrieves the fully qualified class name for this class.
     * 
     * <code>
     *   $o= new lang::Object();
     *   var_dump($o->getClass()->getName());
     * </code>
     *
     * @access  public
     * @return  string name - e.g. "io.File", "rdbms.mysql.MySQL"
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Creates a new instance of the class represented by this Class object.
     * The class is instantiated as if by a new expression.
     *
     * Example:
     * <code>
     *   try {
     *     $o[]= XPClass::forName($name)->newInstance();
     *     $o[]= XPClass::forName($name)->newInstance(TRUE, 1, 'Hello');
     *   } catch(lang::ClassNotFoundException $e) {
     *     // handle it!
     *   }
     * </code>
     *
     * @access  public
     * @return  lang.Object 
     * @throws  lang.ClassNotFoundException when there is no such class
     * @see     xp://lang.XPClass#forName
     */
    public function newInstance() {
      $paramstr= '';
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', func_get_arg('.$i.')';
      }
      
      return eval('return new '.$this->ref.'('.substr($paramstr, 2).');');
    }
    
    /**
     * Gets class methods for this class
     *
     * @access  public
     * @return  string[] methodname
     */
    public function getMethods() {
      return get_class_methods($this->ref);
    }
    
    /**
     * Checks whether this class has a method named "$method" or not.
     * Since in PHP, methods are case-insensitive, calling
     * hasMethod('toString') will provide the same result as 
     * hasMethod('tostring')
     *
     * @access  public
     * @param   string method the method's name
     * @return  bool TRUE if method exists
     */
    public function hasMethod($method) {
      return in_array(
        strtolower($method),
        get_class_methods($this->ref)
      );
    }
    
    /**
     * Retrieve a list of all declared member variables
     *
     * @access  public
     * @return  string[] member names
     */
    public function getFields() {
      return get_class_vars($this->ref);
    }
    
    /**
     * Retrieve the parent class's class object
     *
     * <code>
     *   $o= new lang::Exception();
     *   var_dump($o->getClass()->getParent());
     * </code>
     *
     * @access  public
     * @return  lang.XPClass class object or NULL to indicate no parent class exists
     */
    public function getParent() {
      if (!($parent= get_parent_class($this->ref))) {
        return NULL;
      }
      return new lang::XPClass($parent);
    }
    
    /**
     * Returns the Class object associated with the class with the 
     * given string name. If a classloader is specified, it is used
     * instead of the default classloader (lang.ClassLoader) for situations
     * in which a class does not already exist.
     *
     * <code>
     *   $a= lang::XPClass::forName('de.thekid.cms.Article')->newInstance();
     *   $c= lang::XPClass::forName(
     *     'Category', 
     *     lang::ClassLoader::getInstance('de.thekid.cms')
     *   )->newInstance();
     * </code>
     *
     * @model   static
     * @access  public
     * @param   string name - e.g. "io.File", "rdbms.mysql.MySQL"
     * @param   lang.ClassLoader classloader default NULL
     * @return  lang.XPClass class object
     * @throws  lang.ClassNotFoundException when there is no such class
     */
    public static function forName($name, $classloader= NULL) {
      if (!$classloader) {
        $classloader= lang::ClassLoader::getInstance();
      }
      
      return new lang::XPClass($classloader->loadClass($name));
    }
    
    /**
     * Returns the Class object associated with the given class instance
     *
     * @model   static
     * @access  public
     * @param   object instance
     * @return  lang.XPClass class object
     */
    public static function forInstance($instance) {
      return new lang::XPClass(get_class($instance));
    }
    
    /**
     * Returns an array containing class objects representing all 
     * classes for a given namespace (or, if left empty, all namespaces).
     *
     * @model   static
     * @access  public
     * @param   string namespace default ''
     * @return  lang.XPClass[] class objects
     */
    public static function getClasses($namespace= '') {
      $r= array();
      if ($namespace) {
        $namespace= strtr($namespace, '.', ':');
        $a= array($namespace => get_declared_classes($namespace));
      } else {
        $a= get_declared_classes();
      }
      foreach ($a as $key => $ns) {
        if (is_int($key) || 'php' == $key) continue;
        foreach ($ns as $name) {
          $r[]= new lang::XPClass($key.'::'.$name);
        }
      }
      return $r;
    }
  }
}
?>
