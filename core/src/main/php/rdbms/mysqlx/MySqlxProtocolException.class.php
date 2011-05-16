<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ProtocolException');

  /**
   * Indicate an error was detected in the protocol
   *
   * @see   xp://rdbms.mysqlx.MySqlxProtocol
   */
  class MySqlxProtocolException extends ProtocolException {
    public $error;
    public $sqlstate;
    
    /**
     * Constructor
     *
     * @param   string message
     * @param   int error
     * @param   string sqlstate
     */
    public function __construct($message, $error, $sqlstate) {
      parent::__construct($message);
      $this->error= $error;
      $this->sqlstate= $sqlstate;
    }
  }
?>
