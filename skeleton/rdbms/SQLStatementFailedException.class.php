<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.SQLException');

  /**
   * Indicates an SQL statement sent to the server failed.
   * 
   * @purpose  SQL-Exception
   */
  class SQLStatementFailedException extends SQLException {
    var 
      $sql  = '',
      $code = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     * @param   int code default -1
     */
    function __construct($message, $sql= NULL, $code= -1) {
      parent::__construct($message);
      $this->sql= $sql;
      $this->code= $code;
    }

    /**
     * Get SQL
     *
     * @access  public
     * @return  string
     */
    function getSql() {
      return $this->sql;
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  int
     */
    function getCode() {
      return $this->code;
    }
    
    /**
     * Retrieve string representation of the stack trace
     *
     * @access  publuc
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "Exception %s (Code %s: %s) {\n".
        "  %s\n".
        "}\n",
        $this->getClassName(),
        $this->code,
        $this->message,
        $this->sql
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
?>
