<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::join;

  /**
   * represents a column of a table in a join context
   * This is just a helper class for JoinPart
   *
   * @see     xp://rdbms.join.JoinPart
   * @purpose rdbms.join
   */
  class JoinTableAttribute extends lang::Object {
    private
      $alias= '',
      $name= '',
      $tableName=  '';

    /**
     * Constructor
     *
     * @param   string tableName
     * @param   string name
     */
    public function __construct($tableName, $name) {
      $this->tableName= $tableName;
      $this->name= $name;
      $this->alias= sprintf('%s_%s', $this->tableName, $this->name);;
    }

    /**
     * get sql string
     *
     * @return  string
     */
    public function toSqlString() {
      return sprintf('%s.%s as %s', $this->tableName, $this->name, $this->alias);
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
     * Set tableName
     *
     * @param   string tableName
     */
    public function setTableName($tableName) {
      $this->tableName= $tableName;
    }

    /**
     * Get tableName
     *
     * @return  string
     */
    public function getTableName() {
      return $this->tableName;
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
