<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Represents classes. Every instance of an XP class has an method
   * called getClass() which returns an instance of this class.
   *
   * Warning:
   * Do not construct this class publicly, instead use either the
   * $o->getClass() syntax or the static method 
   * $class= XPClass::forName("fully.qualified.Name")
   *
   * To retrieve the fully qualified name of a class, use this:
   * <code>
   *   $o= new File();
   *   echo 'The class name for $o is '.$o->getClass()->getName();
   * </code>
   *
   * @see lang.Object#getClass()
   */
  class XPClass extends Object {
    private 
      $ref      = NULL,
      $name     = '';
      
    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     */
    private function __construct(&$ref) {
      $this->ref= $ref;
      $this->name= xp::nameOf(is_object($ref) ? get_class($ref) : $ref);
    }
    
    /**
     * Retrieves the fully qualified class name for this class.
     * 
     * Warning: Built-in classes will have a "php." prefixed,
     * e.g. php.stdClass although there is no such directory "php" 
     * in the XP framework and no such file "stdClass.class.php" there.
     *
     * @access  public
     * @return  string name - e.g. "io.File", "rdbms.mysql.MySQL"
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Creates a new instance of the class represented by this Class object.
     * The class is instantiated as if by a new expression with an empty argument list.
     *
     * Example:
     * <code>
     *   try {
     *     $o= XPClass::forName($name)->newInstance();
     *   } catch (ClassNotFoundException $e) {
     *     // handle it!
     *   }
     * </code>
     *
     * @access  public
     * @return  &lang.Object 
     */
    public function newInstance() {
      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      return eval('return new '.xp::reflect($this->name).'('.substr($paramstr, 2).');');
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
     *
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
      return (is_object($this->ref) 
        ? get_object_vars($this->ref) 
        : get_class_vars($this->ref)
      );
    }
    
    /**
     * Retrieve the parent class's class object
     *
     * @access  public
     * @return  &lang.XPClass class object
     */
    public function getParentclass() {
      return new XPClass(get_parent_class($this->ref));
    }
    
    /**
     * Returns the Class object associated with the class with the given string name.
     *
     * @model   static
     * @access  public
     * @param   string name - e.g. "io.File", "rdbms.mysql.MySQL"
     * @return  &lang.XPClass class object
     * @throws  lang.ClassNotFoundException when there is no such class
     */
    public static function forName($name) {
      return new XPClass(ClassLoader::loadClass($name));
    }

    /**
     * Returns the Class object associated with the given instance.
     *
     * @model   static
     * @access  public
     * @param   &lang.Object instance
     * @return  &lang.XPClass class object
     * @throws  lang.ClassNotFoundException when there is no such class
     */
    public static function forInstance($instance) {
      return new XPClass($instance);
    }
    
    /**
     * Returns an array containing class objects representing all the public classes
     *
     * @model   static
     * @access  public
     * @return  &lang.XPClass[] class objects
     */
    public static function getClasses() {
      $ret= array();
      foreach (get_declared_classes() as $name) {
        $ret[]= new XPClass($name);
      }
      return $ret;
    }
  }
?>
