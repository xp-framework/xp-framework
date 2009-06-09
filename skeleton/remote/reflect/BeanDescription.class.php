<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.HashSet');

  define('HOME_INTERFACE',    0);
  define('REMOTE_INTERFACE',  1);

  /**
   * Describes an EJB
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class BeanDescription extends Object {
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
      $set= new HashSet();
      for ($i= 0; $i < $this->interfaces->length; $i++) {
        $set->addAll($this->interfaces[$i]->classSet());
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
        xp::stringOf($this->interfaces[HOME_INTERFACE]),
        xp::stringOf($this->interfaces[REMOTE_INTERFACE])
      );
    }
  }
?>
