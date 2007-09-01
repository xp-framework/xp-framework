<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::join;

  /**
   * Represents a column of a table in a join context.
   * Helper class for JoinPart.
   *
   * @purpose rdbms.join
   * @see     xp://rdbms.join.JoinPart
   */
  class JoinTable extends lang::Object {
    private
      $alias= '',
      $name=  '';

    /**
     * Constructor
     *
     * @param   string name
     * @param   string alias
     */
    public function __construct($name, $alias) {
      $this->alias= $alias;
      $this->name=  $name;
    }

    /**
     * get sql string
     *
     * @return  string
     */
    public function toSqlString() {
      return sprintf('%s as %s', $this->name, $this->alias);
    }

    /**
     * Set alias
     *
     * @param   string alias
     */
    public function setAlias($alias) {
      $this->alias= $alias;
    }

    /**
     * Get alias
     *
     * @return  string
     */
    public function getAlias() {
      return $this->alias;
    }

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
