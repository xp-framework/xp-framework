<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.SQLException');

  /**
   * Indicates connection to the server failed.
   * 
   * @purpose  SQL-Exception
   */
  class SQLConnectException extends SQLException {
    public 
      $dsn  = NULL;

    /**
     * Constructor
     *
     * @param   string message
     * @param   rdbms.DSN dsn
     */
    public function __construct($message, $dsn) {
      parent::__construct($message);
      $this->dsn= $dsn;
    }

    /**
     * Get DSN used for connect
     *
     * @return  rdbms.DSN
     */
    public function getDsn() {
      return $this->dsn;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (%s) {\n".
        "  Unable to connect to %s@%s - using password: %s\n".
        "}\n",
        $this->getClassName(),
        $this->message,
        $this->dsn->getUser(),
        $this->dsn->getHost(),
        $this->dsn->getPassword() ? 'yes' : 'no'
      );
    }
  }
?>
