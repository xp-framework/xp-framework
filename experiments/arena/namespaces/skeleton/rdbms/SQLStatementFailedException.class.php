<?php
/* This class is part of the XP framework
 *
 * $Id: SQLStatementFailedException.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace rdbms;

  uses('rdbms.SQLException');

  /**
   * Indicates an SQL statement sent to the server failed.
   * 
   * @purpose  SQL-Exception
   */
  class SQLStatementFailedException extends SQLException {
    public 
      $sql  = '',
      $errorcode = 0;

    /**
     * Constructor
     *
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     * @param   int errorcode default -1
     */
    public function __construct($message, $sql= , $errorcode= -1) {
      parent::__construct($message);
      $this->sql= $sql;
      $this->errorcode= $errorcode;
    }

    /**
     * Get SQL
     *
     * @return  string
     */
    public function getSql() {
      return $this->sql;
    }

    /**
     * Get errorcode
     *
     * @return  int
     */
    public function getErrorcode() {
      return $this->errorcode;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (errorcode %s: %s) {\n".
        "  %s\n".
        "}\n",
        $this->getClassName(),
        $this->errorcode,
        $this->message,
        $this->sql
      );
    }
  }
?>
