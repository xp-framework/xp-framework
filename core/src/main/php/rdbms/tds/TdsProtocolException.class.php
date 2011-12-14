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
    public function __construct($message, $number= 0, $state= 0, $class= 0, $server= NULL, $proc= NULL, $line= 0) {
      parent::__construct($message);
      $this->number= $number;
      $this->state= $state;
      $this->class= $class;
      $this->server= $server;
      $this->proc= $proc;
      $this->line= $line;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      $addr= array_filter(array($this->server, $this->proc, $this->line));
      return sprintf(
        'Exception %s (#%d, state %d, class %d: %s%s)',
        $this->getClassName(),
        $this->number,
        $this->state,
        $this->class,
        $this->message,
        $addr ? ' @ '.implode(':', $addr) : ''
      );
    }
  }
?>
