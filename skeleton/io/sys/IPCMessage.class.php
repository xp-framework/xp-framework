<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent an IPC message
   *
   * @ext      http://de3.php.net/manual/en/ref.sem.php
   * @purpose  create an ipc message
   */
  class IPCMessage extends Object {
    var
      $message        = '',
      $type           = 0;

    /**
     * Constructor
     *
     * @access  private
     * @param   
     */      
    function __construct($message, $type= 1) {
      $this->message= $message;
      $this->type= $type;
    }

    /**
     * Set Message
     *
     * @access  public
     * @param   string message
     */
    function setMessage($message) {
      $this->message= $message;
    }

    /**
     * Get Message
     *
     * @access  public
     * @return  string
     */
    function getMessage() {
      return $this->message;
    }

    /**
     * Set Type
     *
     * @access  public
     * @param   mixed type
     */
    function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  mixed
     */
    function getType() {
      return $this->type;
    }
  }
?>
