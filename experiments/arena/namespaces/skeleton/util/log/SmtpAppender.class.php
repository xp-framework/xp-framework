<?php
/* This class is part of the XP framework
 *
 * $Id: SmtpAppender.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace util::log;

  ::uses('util.log.LogAppender');

  /**
   * LogAppender which sends log to an email address
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Appender
   */  
  class SmtpAppender extends LogAppender {
    public 
      $email    = '',
      $prefix   = '',
      $sync     = TRUE;
      
    public
      $_data    = array();
    
    /**
     * Constructor
     *
     * @param   string email the email address to send log entries to
     * @param   string prefix
     * @param   bool sync default TRUE
     */
    public function __construct($email= NULL, $prefix= '', $sync= TRUE) {
      $this->email= $email;
      $this->prefix= $prefix;
      $this->sync= $sync;
    }
    
    /**
     * Destructor
     *
     */
    public function __destruct() {
      $this->finalize();
    }
    
    /**
     * Sends log data to the specified email address
     *
     * @param   mixed args variables
     */
    public function append() {
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
     */
    public function finalize() {
      if ($this->sync || 0 == sizeof($this->_data)) return;
      
      $body= '';
      foreach ($this->_data as $line) {
        $body.= $line."\n";
      }

      mail($this->email, $this->prefix.' ['.(sizeof($this->_data)).' entries]', $body);
    }
  }
?>
