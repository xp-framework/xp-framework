<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
uses('lang.StackTraceElement');

namespace lang { 
 
  /**
   * Exception
   *
   * @purpose  Base class for all other exceptions
   * @see      http://java.sun.com/docs/books/tutorial/essential/exceptions/definition.html
   */
  class Throwable extends lang::Object {
    public 
      $message  = '',
      $trace    = array();
     
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     */
    public function __construct($message) {
      $this->message= $message;
      foreach (debug_backtrace() as $trace) {
        $this->trace[]= new lang::StackTraceElement(
          $trace['file'],
          $trace['class'],
          $trace['function'],
          $trace['line'],
          @xp::registry::$errors[$trace['file']]
        );
      }
    }
    
    /**
     * Print "stacktrace" to standard output
     *
     * @see     xp://lang.Exception#toString
     * @access  public
     */
    public function printStackTrace() {
      print self::toString();
    }
    
    /**
     * Return formatted output of stacktrace
     *
     * Example:
     * <pre>
     * Exception io.IOException (Could not connect to localFOO:21 within 4 seconds)
     *   at io.IOException:__construct (from FtpConnection.class.php, line 119)
     *   at peer.ftp.FtpConnection:connect (from FtpConnection.class.php, line 103:
     *     ftp_connect() [http://www.php.net/function.ftp-connect]:
     *     php_network_getaddresses: getaddrinfo failed: No address associated with
     *     hostname
     *   )
     *   at peer.ftp.FtpConnection:connect (from test.php, line 7)
     *   at test.php:<main> (from test.php, line 5:
     *     Use of undefined constant FOO - assumed 'FOO'
     *   )
     * </pre>
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "Exception %s (%s)\n",
        self::getClassName(),
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
    
    /**
     * Return an array of stack trace elements
     *
     * @access  public
     * @return  lang.StackTraceElement[] array of stack trace elements
     * @see     xp://lang.StackTraceElement
     */
    public function getStackTrace() {
      return $this->trace;
    }
    
    /**
     * Return the message
     *
     * @access  public
     * @return  string message
     */
    public function getMessage() {
      return $this->message;
    }

  }
}
?>
