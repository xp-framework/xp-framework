<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.HashSet');

  /**
   * Describes an EJB interface
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class InterfaceDescription extends Object {
    public 
      $className = '',
      $methods   = NULL;

    /**
     * Set ClassName
     *
     * @access  public
     * @param   string className
     */
    public function setClassName($className) {
      $this->className= $className;
    }

    /**
     * Get ClassName
     *
     * @access  public
     * @return  string
     */
    public function getClassName() {
      return $this->className;
    }

    /**
     * Set Methods
     *
     * @access  public
     * @param   lang.ArrayList<remote.reflect.MethodDescription> methods
     */
    public function setMethods(&$methods) {
      $this->methods= &$methods;
    }

    /**
     * Get Methods
     *
     * @access  public
     * @return  lang.ArrayList<remote.reflect.MethodDescription>
     */
    public function &getMethods() {
      return $this->methods;
    }

    /**
     * Retrieve a set of classes used in this interface
     *
     * @access  public
     * @return  remote.ClassReference[]
     */
    public function classSet() {
      $set= &new HashSet(); 
      for ($i= 0, $s= sizeof($this->methods->values); $i < $s; $i++) {
        $set->addAll($this->methods->values[$i]->classSet()); 
      }
      return $set->toArray();
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $r= $this->getClassName().'@(class= '.$this->className.") {\n";
      for ($i= 0, $s= sizeof($this->methods->values); $i < $s; $i++) {
        $r.= '  - '.str_replace("\n", "\n  ", $this->methods->values[$i]->toString())."\n";
      }
      return $r.'}';
    }    
  }
?>
