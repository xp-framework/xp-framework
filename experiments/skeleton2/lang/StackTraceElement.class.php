<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

namespace lang { 

  /**
   * An element in a stack trace, as returned by Exception.getStackTrace(). 
   * Each element represents a single stack frame.
   *
   * @see      xp://lang.Exception#getStackTrace()
   * @purpose  purpose
   */
  class StackTraceElement extends lang::Object {
    public
      $file     = '',
      $class    = '',
      $method   = '',
      $line     = 0,
      $messages = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string file
     * @param   string class
     * @param   string method
     * @param   int line
     * @param   array messages
     */
    public function __construct($file, $class, $method, $line, $messages= array()) {
      $this->file     = $file;  
      $this->class    = $class; 
      $this->method   = $method;
      $this->line     = $line;
      $this->messages = $messages;
    }
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $fmt= sprintf(
        "  at %s::%s (from %s, line %%3\$d) %%2\$s\n",
        $this->class,
        $this->method,
        basename($this->file)
      );
      
      if (!$this->messages) {
        return sprintf($fmt, E_USER_NOTICE, '', $this->line);
      }
      
      $str= '';
      for ($i= 0, $s= sizeof($this->messages); $i < $s; $i++) {
        $str.= vsprintf($fmt, $this->messages[$i]);
      }
      return $str;
    }
  }
}
?>
