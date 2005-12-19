<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogAppender');

  /**
   * LogAppender which sends log to an email address
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class SmtpAppender extends LogAppender {
    var 
      $email    = '',
      $prefix   = '',
      $sync     = TRUE;
      
    var
      $_data    = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string email the email address to send log entries to
     * @param   string prefix
     * @param   bool sync default TRUE
     */
    function __construct($email= NULL, $prefix= '', $sync= TRUE) {
      $this->email= $email;
      $this->prefix= $prefix;
      $this->sync= $sync;
    }
    
    /**
     * Destructor
     *
     * @access  protected
     */
    function __destruct() {
      $this->finalize();
    }
    
    /**
     * Sends log data to the specified email address
     *
     * @access  public
     * @param   mixed args variables
     */
    function append() {
      $body= '';
      
      with ($args= func_get_args()); {
        foreach ($args as $idx => $arg) {
          $body.= $this->varSource($arg).($idx < sizeof($args)-1 ? ' ' : '');
        }
      }
      
      if ($this->sync) {
        mail($this->email, $this->prefix, $body);
      } else {
        $this->_data[]= $body;
      }
    }
    
    /**
     * Finalize this appender - is called when the logger shuts down
     * at the end of the request.
     *
     * @access  public 
     */
    function finalize() {
      if ($this->sync) return;
      
      $body= '';
      foreach ($this->_data as $line) {
        $body.= $line."\n";
      }

      mail($this->email, $this->prefix.' ['.(sizeof($this->_data)).' entries]', $body);
    }
  }
?>
