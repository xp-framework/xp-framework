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
    protected
      $sql  = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     * @param   int code default -1
     */
    public function __construct($message, $sql= NULL, $code= -1) {
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
    public function getSql() {
      return $this->sql;
    }

    /**
     * Retrieve string representation of the stack trace
     *
     * @access  publuc
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "Exception %s (Code %s: %s) {\n".
        "  %s\n".
        "}\n",
        self::getClassName(),
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
