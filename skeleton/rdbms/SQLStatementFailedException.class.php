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
    public 
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
    public function __construct($message, $sql= NULL, $errorcode= -1) {
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
    public function getSql() {
      return $this->sql;
    }

    /**
     * Get errorcode
     *
     * @access  public
     * @return  int
     */
    public function getErrorcode() {
      return $this->errorcode;
    }

    /**
     * Return compound message of this exception.
     *
     * @access  public
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
