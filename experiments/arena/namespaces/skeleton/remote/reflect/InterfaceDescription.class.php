<?php
/* This class is part of the XP framework
 *
 * $Id: InterfaceDescription.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace remote::reflect;

  uses('util.collections.HashSet');

  /**
   * Describes an EJB interface
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class InterfaceDescription extends lang::Object {
    public 
      $className = '',
      $methods   = NULL;

    /**
     * Set ClassName
     *
     * @param   string className
     */
    public function setClassName($className) {
      $this->className= $className;
    }

    /**
     * Get ClassName
     *
     * @return  string
     */
    public function getClassName() {
      return $this->className;
    }

    /**
     * Set Methods
     *
     * @param   lang.ArrayList<remote.reflect.MethodDescription> methods
     */
    public function setMethods($methods) {
      $this->methods= $methods;
    }

    /**
     * Get Methods
     *
     * @return  lang.ArrayList<remote.reflect.MethodDescription>
     */
    public function getMethods() {
      return $this->methods;
    }

    /**
     * Retrieve a set of classes used in this interface
     *
     * @return  remote.ClassReference[]
     */
    public function classSet() {
      $set= new util::collections::HashSet(); 
      for ($i= 0, $s= sizeof($this->methods->values); $i < $s; $i++) {
        $set->addAll($this->methods->values[$i]->classSet()); 
      }
      return $set->toArray();
    }

    /**
     * Creates a string representation of this object
     *
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
