<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for a telephony provider
   * 
   */
  class TelephonyProvider extends Object {
    var
      $cat  = NULL;
      
    /**
     * Set a LogCategory for tracing communication
     *
     * @access  public
     * @param   &util.log.LogCategory cat a LogCategory object to which communication
     *          information will be passed to or NULL to stop tracing
     * @throws  IllegalArgumentException in case a of a type mismatch
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
    
    /**
     * Connect and initiate the communication
     *
     * @access	public
     */
    function connect() { }

    /**
     * Connect and initiate the communication
     *
     * @access	public
     */
    function close() { }
    
    
  }
?>
