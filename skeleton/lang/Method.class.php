<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a class method
   *
   * @see      xp://lang.XPClass
   * @purpose  Reflection
   */
  class Method extends Object {
    var
      $_ref = NULL,
      $name = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     * @param   string name
     */    
    function __construct(&$ref, $name) {
      parent::__construct();
      $this->_ref= &$ref;
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the method represented by this Method object.
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &getDeclaringClass() {
      $c= is_object($this->_ref) ? get_class($this->_ref) : $this->_ref;
      do {
        $p= get_parent_class($c);
        if (!$p || !is_callable(array($p, $this->name))) break;
      } while ($c= $p);

      return new XPClass($c);
    }
    
    /**
     * Invokes the underlying method represented by this Method object, 
     * on the specified object with the specified parameters.
     *
     * @access  public
     * @param   &lang.Object obj
     * @param   mixed* args
     * @return  &mixed
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     */
    function &invoke(&$obj) {
      if (!is(xp::nameOf($this->_ref), $obj)) {
        return throw(new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_ref),
          xp::nameOf($obj)
        )));
      }
      
      $a= func_get_args();
      return call_user_func_array(array(&$obj, $this->name), array_slice($a, 1));
    }
  }
?>
