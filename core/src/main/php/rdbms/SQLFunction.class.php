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
   * Represents an SQL standard procedure
   *
   * @purpose  SQL Fragment implementation
   */
  class SQLFunction extends Object implements SQLFragment {
    public
      $func = '',
      $type = '%s',
      $args = array();

    /**
     * Constructor
     *
     * @param   string function
     * @param   string type one of the %-tokens
     * @param   var[] arguments
     */
    public function __construct($function, $type, $arguments= array()) {
      $this->func= $function;
      $this->type= $type;
      $this->args= $arguments;
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
      return call_user_func_array(array($conn, 'prepare'), $args);
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
     * @param   var[] args
     */
    public function setArgs($args) {
      $this->args= $args;
    }

    /**
     * Get args
     *
     * @return  var[]
     */
    public function getArgs() {
      return $this->args;
    }

    /**
     * Return type this function evaluates to
     *
     * @return  string
     */
    public function getType() {
      return $this->type; 
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->type.' '.$this->func.' ('.xp::stringOf($this->args).')>';
    }
  }
?>
