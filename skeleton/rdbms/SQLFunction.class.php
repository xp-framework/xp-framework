<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'rdbms.SQLDialect',
    'rdbms.SQLFragment'
  );

  /**
   * represents an SQL standard procedure
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SQLFunction extends Object implements SQLFragment {
    
    public
      $func= '',
      $args= array();

    /**
     * Constructor
     *
     * @param   string function
     */
    public function __construct() {
      $args= func_get_args();
      $this->func= array_shift($args);
      $this->args= $args;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn) {
      $args= $this->args;
      array_unshift($args, $conn->getFormatter()->dialect->formatFunction($this));
      return (call_user_func_array(array($conn, 'prepare'), $args));
    }

    /**
     * Set func
     *
     * @param   string func
     */
    public function setFunc($func) {
      $this->func= $func;
    }

    /**
     * Get func
     *
     * @return  string
     */
    public function getFunc() {
      return $this->func;
    }

    /**
     * Set args
     *
     * @param   mixed[] args
     */
    public function setArgs($args) {
      $this->args= $args;
    }

    /**
     * Get args
     *
     * @return  mixed[]
     */
    public function getArgs() {
      return $this->args;
    }

  }
?>
