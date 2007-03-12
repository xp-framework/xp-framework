<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a database constaint
   */
  abstract class DBConstraint extends Object {

    public 
      $name= '';

    /**
     * Set name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
  }
?>
