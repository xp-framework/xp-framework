<?php
/* This class is part of the XP framework
 *
 * $Id: BeanDescription.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace remote::reflect;

  uses('util.collections.HashSet');

  define('HOME_INTERFACE',    0);
  define('REMOTE_INTERFACE',  1);

  /**
   * Describes an EJB
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class BeanDescription extends lang::Object {
    public 
      $jndiName   = '',
      $interfaces = NULL;

    /**
     * Set JndiName
     *
     * @param   string jndiName
     */
    public function setJndiName($jndiName) {
      $this->jndiName= $jndiName;
    }

    /**
     * Get JndiName
     *
     * @return  string
     */
    public function getJndiName() {
      return $this->jndiName;
    }

    /**
     * Set Interfaces
     *
     * @param   lang.ArrayList<remote.reflect.InterfaceDescription> interfaces
     */
    public function setInterfaces($interfaces) {
      $this->interfaces= $interfaces;
    }

    /**
     * Get Interfaces
     *
     * @return  lang.ArrayList<remote.reflect.InterfaceDescription>
     */
    public function getInterfaces() {
      return $this->interfaces;
    }
    
    /**
     * Return a unique list of all classes used in this bean's interfaces
     *
     * @return  remote.ClassReference[]
     */
    public function classSet() {
      $set= new util::collections::HashSet();
      foreach (array_keys($this->interfaces->values) as $kind) {
        $set->addAll($this->interfaces->values[$kind]->classSet());
      }
      return $set->toArray();
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s@(jndi= %s) {\n".
        "  [Home  ]: %s\n".
        "  [Remote]: %s\n".
        "}",
        $this->getClassName(),
        $this->jndiName,
        ::xp::stringOf($this->interfaces->values[HOME_INTERFACE]),
        ::xp::stringOf($this->interfaces->values[REMOTE_INTERFACE])
      );
    }
  }
?>
