<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */
 
  /**
   * NotifiedException
   *
   * Example of usage:
   * ----------------
   * <pre>
   *   throw(new NotifiedException(
   *     'Something weird happened',
   *     array(
   *        'mail' => 'your@email.tld'
   *     ),
   *     $details
   *   ));
   * </pre>
   *
   * User defined handlers for notify methods such as SMS, Log, OnScreen-Popup, 
   * Digest, Escalation, ... may be defined as util.notify.<<METHOD_NAME>>Notifier
   * 
   * @deprecated
   * @see Exception
   */
  class NotifiedException extends Exception {
    var
      $notified= array();
      
    var
      $notify,
      $details;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string messsage
     * @param   array notify Associative array (method => params) 
     * @param   string details
     */
    function __construct($message, $notify, $details) {
      parent::__construct($message);
      $this->notify= $notify;
      $this->details= $details;

      // Look for notifiers
      $reflect= array();
      foreach ($this->notify as $method=> $params) {
        $notifier= ClassLoader::loadClass('util.notify.'.ucfirst($method).'Notifier');
        $this->notified[$method]= FALSE;
        if (FALSE === $notifier) unset($this->notify[$method]);
        
        $reflect[$method]= $notifier;
      }
      
      // Send out Notify when thrown. We do not know yet if notifies 
      // will be OK, so simply don't print anything (return value is not
      // defined yet:-))
      $stack= $this->toString('');
      foreach ($this->notify as $method=> $params) {
      
        // Create an instance and call notify()-method
        $n= &new $reflect[$method]();
        $this->notified[$method]= $n->notify($message, $params, $details, $stack);
        $n->__destruct();
      }
    }
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    function toString($e= 'failed') {
      $notify= '';
      foreach ($this->notified as $method=> $result) {
        $notify.= sprintf("      [%-20s] %s\n", $method, ($result ? 'succeeded' : $e));
      }
      
      return (
        parent::toString().
        '  *** Notify via methods:'.
        $notify
      );
    }
  }
?>
