<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ProtocolException');

  /**
   * Indicate an error was detected in the protocol
   *
   * @see   xp://rdbms.tds.TdsV7Protocol
   */
  class TdsProtocolException extends ProtocolException {
    public $number;
    public $state;
    public $class;
    public $server;
    public $proc;
    public $line;
    
    /**
     * Constructor
     *
     * @param   string message
     * @param   int number
     * @param   int state
     * @param   int class
     * @param   string server
     * @param   string proc
     * @param   int line
     */
    public function __construct($message, $number, $state, $class, $server, $proc, $line) {
      parent::__construct($message);
      $this->number= $number;
      $this->state= $state;
      $this->class= $class;
      $this->server= $server;
      $this->proc= $proc;
      $this->line= $line;
    }
  }
?>
