<?php
/* This class is part of the XP framework
 *
 * $Id: Person.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::remote;

  /**
   * Person object
   *
   * @see      xp://net.xp_framework.unittest.remote.SerializerTest
   * @purpose  Helper class for SerializerTest
   */
  class Person extends lang::Object {
    public
      $id     = 1549,
      $name   = 'Timm Friebe';

    /**
     * Set Id
     *
     * @param   mixed id
     */
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @return  mixed
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Set Name
     *
     * @param   mixed name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  mixed
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Checks whether a given object is equal to this person.
     *
     * @param   &lang.Object cmp
     * @return  bool
     */
    public function equals($cmp) {
      return ::is('Person', $cmp) && $cmp->name == $this->name && $cmp->id == $this->id;
    }
  }
?>
