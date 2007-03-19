<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses ('rdbms.DBConstraint');

  /**
   * Represents a database foreign key constraint
   *
   * @see      rdbms.DBConstraint
   */
  class DBForeignKeyConstraint extends DBConstraint {
    public
      $keys=   array(),
      $source= '';

    /**
     * Set keys
     *
     * @param    string attribute in the current table
     * @param    string attribute in the referenced table
     */
    public function addKey($attribute, $sourceAttribute) {
      $this->keys[$attribute]= $sourceAttribute;
    }

    /**
     * Set keys
     *
     * @param   string[] keys
     */
    public function setKeys($keys) {
      $this->keys= $keys;
    }

    /**
     * Get keys
     *
     * @return  string[]
     */
    public function getKeys() {
      return $this->keys;
    }

    /**
     * Set source
     *
     * @param   string source
     */
    public function setSource($source) {
      $this->source= $source;
    }

    /**
     * Get source
     *
     * @return  string
     */
    public function getSource() {
      return $this->source;
    }

  }
?>
