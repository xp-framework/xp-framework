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
   * $class= &XPClass::forName("fully.qualified.Name")
   *
   * To retrieve the fully qualified name of a class, use this:
   * <code>
   *   $o= &new File();
   *   $c= &$o->getClass();
   *   echo 'The class name for $o is '.$c->getName();
   * </code>
   *
   * @see lang.Object#getClass()
   */
  class XPClass extends Object {
    var 
      $_objref  = NULL,
      $name     = '';
      
    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     */
    function __construct(&$ref) {
      $this->_objref= &$ref;
      $this->name= xp::nameOf(is_object($ref) ? get_class($ref) : $ref);
      parent::__construct();
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
    function getName() {
      return $this->name;
    }
    
    /**
     * Creates a new instance of the class represented by this Class object.
     * The class is instantiated as if by a new expression with an empty argument list.
     *
     * Example:
     * <code>
     *   try(); {
     *     $c= &XPClass::forName($name) &&
     *     $o= &$c->newInstance();
     *   } if (catch('ClassNotFoundException', $e)) {
     *     // handle it!
     *   }
     * </code>
     *
     * @access  public
     * @return  &lang.Object 
     */
    function &newInstance() {
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
    function getMethods() {
      return get_class_methods($this->_objref);
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
    function hasMethod($method) {
      return in_array(
        strtolower($method),
        get_class_methods($this->_objref)
      );
    }
    
    /**
     * Retrieve a list of all declared member variables
     *
     * @access  public
     * @return  string[] member names
     */
    function getFields() {
      return (is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      );
    }
    
    /**
     * Retrieve the parent class's class object
     *
     * @access  public
     * @return  &lang.XPClass class object
     */
    function &getParentclass() {
      return new XPClass(get_parent_class($this->_objref));
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
    function &forName($name) {
      if (!($c= ClassLoader::loadClass($name))) return $c;
      return new XPClass($c);
    }
    
    /**
     * Returns an array containing class objects representing all the public classes
     *
     * @model   static
     * @access  public
     * @return  &lang.XPClass[] class objects
     */
    function getClasses() {
      $ret= array();
      foreach (get_declared_classes() as $name) {
        $ret[]= &new XPClass($name);
      }
      return $ret;
    }
  }
?>
