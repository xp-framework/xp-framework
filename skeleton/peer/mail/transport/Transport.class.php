<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('peer.mail.transport.TransportException');
  
  /**
   * Abstract base class for mail transport
   *
   * @purpose  Provide an interface
   */
  class Transport extends Object {
    var
      $cat    = NULL;

    /**
     * Connect to this transport
     *
     * @access  abstract
     * @param   string dsn default NULL
     */
    function connect($dsn= NULL) { }
    
    /**
     * Close connection
     *
     * @access  abstract
     */
    function close() { }
  
    /**
     * Send a message
     *
     * @access  abstract
     * @param   &peer.mail.Message message the Message object to send
     * @throws  TransportException to indicate an error occured
     */
    function send(&$message) { }
    
    /**
     * Set a LogCategory for tracing communication
     *
     * @access  public
     * @param   &util.log.LogCategory cat a LogCategory object to which communication
     *          information will be passed to or NULL to stop tracing
     * @return  &util.log.LogCategory
     * @throws  lang.IllegalArgumentException in case a of a type mismatch
     */
    function &setTrace(&$cat) {
      if (NULL !== $cat && !is_a($cat, 'LogCategory')) {
        return throw(new IllegalArgumentException('Argument passed is not a LogCategory'));
      }
      
      $this->cat= &$cat;
    }
    
    /**
     * Trace function
     *
     * @access  protected
     * @param   mixed* arguments
     */
    function trace() {
      if (NULL == $this->cat) return;
      $args= func_get_args();
      call_user_func_array(array($this->cat, 'debug'), $args);
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
