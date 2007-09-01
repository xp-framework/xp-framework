<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms;

  /**
   * Represents a database constaint
   */
  abstract class DBConstraint extends lang::Object {

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
