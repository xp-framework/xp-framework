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
      $errorcode = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     * @param   int errorcode default -1
     */
    function __construct($message, $sql= NULL, $errorcode= -1) {
      parent::__construct($message);
      $this->sql= $sql;
      $this->errorcode= $errorcode;
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
     * Get errorcode
     *
     * @access  public
     * @return  int
     */
    function getErrorcode() {
      return $this->errorcode;
    }
    
    /**
     * Retrieve string representation of the stack trace
     *
     * @access  publuc
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "Exception %s (errorcode %s: %s) {\n".
        "  %s\n".
        "}\n",
        $this->getClassName(),
        $this->errorcode,
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
