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
    public
      $email    = '',
      $prefix   = '',
      $sync     = TRUE;
      
    protected
      $_data    = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string email the email address to send log entries to
     * @param   string prefix
     * @param   bool sync default TRUE
     */
    public function __construct($email= NULL, $prefix= '', $sync= TRUE) {
      $this->email= $email;
      $this->prefix= $prefix;
      $this->sync= $sync;
      parent::__construct();
    }
    
    /**
     * Sends log data to the specified email address
     *
     * @access  public
     * @param   mixed args variables
     */
    public function append() {
      $body= '';
      for ($i= 1, $s= func_num_args(); $i < $s; $i++) {
        $arg= func_get_arg($i);
        $body.= sprintf("[%08x] %s\n", $i, self::varSource($arg));
      }

      if ($this->sync) { 
        $arg= func_get_arg(0);
        mail($this->email, $this->prefix.$arg, $body);
      } else {
        $arg= func_get_arg(0);
        $this->_data[]= array($arg, $body);
      }
    }
    
    /**
     * Finalize this appender - is called when the logger shuts down
     * at the end of the request.
     *
     * @access  public 
     */
    public function finalize() {
      if ($this->sync) return;
      
      $body= '';
      for ($i= 1, $s= sizeof($this->_data); $i < $s; $i++) {
        $body.= (
          str_pad($this->_data[$i][0], 72, '-', STR_PAD_BOTH).
          "\n".
          $this->_data[$i][1]
        );
      }

      mail($this->email, $this->prefix.$this->_data[0][0].' [+'.$s.']', $body);
    }
  }
?>
