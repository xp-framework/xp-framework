<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Describes an EJB interface
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class InterfaceDescription extends Object {
    var 
      $className = '',
      $methods   = array();

    /**
     * Set ClassName
     *
     * @access  public
     * @param   string className
     */
    function setClassName($className) {
      $this->className= $className;
    }

    /**
     * Get ClassName
     *
     * @access  public
     * @return  string
     */
    function getClassName() {
      return $this->className;
    }

    /**
     * Set Methods
     *
     * @access  public
     * @param   mixed[] methods
     */
    function setMethods($methods) {
      $this->methods= $methods;
    }

    /**
     * Get Methods
     *
     * @access  public
     * @return  mixed[]
     */
    function getMethods() {
      return $this->methods;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $r= $this->getClassName().'@(class= '.$this->className.") {\n";
      for ($i= 0, $s= sizeof($this->methods); $i < $s; $i++) {
        $r.= '  - '.str_replace("\n", "\n  ", $this->methods[$i]->toString())."\n";
      }
      return $r.'}';
    }    
  }
?>
