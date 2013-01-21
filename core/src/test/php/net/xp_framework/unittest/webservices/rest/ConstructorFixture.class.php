<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.webservices.rest';

  /**
   * Issues
   *
   */
  abstract class net·xp_framework·unittest·webservices·rest·ConstructorFixture extends Object {
    public $id= 0;

    /**
     * Check whether another object is equal to this
     * 
     * @param   var cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->id === $this->id;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@'.$this->id;
    }
  }
?>
