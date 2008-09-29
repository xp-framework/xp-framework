<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('peer.mail.transport.TransportException', 'util.log.Traceable');
  
  /**
   * Abstract base class for mail transport
   *
   * @purpose  Provide an interface
   */
  class Transport extends Object implements Traceable {
    public
      $cat    = NULL;

    /**
     * Connect to this transport
     *
     * @param   string dsn default NULL
     */
    public function connect($dsn= NULL) { }
    
    /**
     * Close connection
     *
     */
    public function close() { }
  
    /**
     * Send a message
     *
     * @param   peer.mail.Message message the Message object to send
     * @throws  peer.mail.transport.TransportException to indicate an error occured
     */
    public function send($message) { }
    
    /**
     * Set a LogCategory for tracing communication
     *
     * @param   util.log.LogCategory cat a LogCategory object to which communication
     *          information will be passed to or NULL to stop tracing
     * @return  util.log.LogCategory
     * @throws  lang.IllegalArgumentException in case a of a type mismatch
     */
    public function setTrace($cat) {
      if (NULL !== $cat && !$cat instanceof LogCategory) {
        throw(new IllegalArgumentException('Argument passed is not a LogCategory'));
      }
      
      $this->cat= $cat;
    }
    
    /**
     * Trace function
     *
     * @param   mixed* arguments
     */
    protected function trace() {
      if (NULL == $this->cat) return;
      $args= func_get_args();
      call_user_func_array(array($this->cat, 'debug'), $args);
    }

  } 
?>
