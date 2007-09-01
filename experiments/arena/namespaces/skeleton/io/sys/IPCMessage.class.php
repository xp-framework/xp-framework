<?php
/* This class is part of the XP framework
 *
 * $Id: IPCMessage.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace io::sys;

  /**
   * Represent an IPC message
   *
   * @ext      http://de3.php.net/manual/en/ref.sem.php
   * @purpose  create an ipc message
   */
  class IPCMessage extends lang::Object {
    public
      $message        = '',
      $type           = 0;

    /**
     * Constructor
     *
     * @param   string message
     * @param   int messagetype
     */      
    public function __construct($message, $type= 1) {
      $this->message= $message;
      $this->type= $type;
    }

    /**
     * Set Message
     *
     * @param   string message
     */
    public function setMessage($message) {
      $this->message= $message;
    }

    /**
     * Get Message
     *
     * @return  string
     */
    public function getMessage() {
      return $this->message;
    }

    /**
     * Set Type
     *
     * @param   mixed type
     */
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @return  mixed
     */
    public function getType() {
      return $this->type;
    }
  }
?>
