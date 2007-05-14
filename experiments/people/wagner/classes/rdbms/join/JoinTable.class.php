<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * groups table data for joins
   *
   */
  class JoinTable extends Object {
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
