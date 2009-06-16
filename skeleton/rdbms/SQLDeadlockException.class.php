<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.SQLStatementFailedException');

  /**
   * Indicates a deadlock occured
   * 
   * @purpose  SQL-Exception
   */
  class SQLDeadlockException extends SQLStatementFailedException {
    protected $sql= '';

    /**
     * Constructor
     *
     * @param   string message
     * @param   string sql default NULL the SQL query string sent
     */
    public function __construct($message, $sql= NULL) {
      parent::__construct($message);
      $this->sql= $sql;
    }

    /**
     * Get SQL leading to this deadlock
     *
     * @return  string
     */
    public function getSql() {
      return $this->sql;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (deadlock: %s) {\n".
        "  %s\n".
        "}\n",
        $this->getClassName(),
        $this->message,
        $this->sql
      );
    }
  }
?>
