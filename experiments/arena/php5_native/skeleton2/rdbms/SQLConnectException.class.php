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
     * @access  public
     * @param   string message
     * @param   &rdbms.DSN dsn
     */
    public function __construct($message, &$dsn) {
      parent::__construct($message);
      $this->dsn= &$dsn;
    }

    /**
     * Get DSN used for connect
     *
     * @access  public
     * @return  &rdbms.DSN
     */
    public function &getDsn() {
      return $this->dsn;
    }

    /**
     * Retrieve string representation of the stack trace
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "Exception %s (%s) {\n".
        "  Unable to connect to %s@%s - using password: %s\n".
        "}\n",
        $this->getClassName(),
        $this->message,
        $this->dsn->getUser(),
        $this->dsn->getHost(),
        $this->dsn->getPassword() ? 'yes' : 'no'
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
?>
