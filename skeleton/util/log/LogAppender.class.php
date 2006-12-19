<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for appenders
   *
   * @see      xp://util.log.LogCategory#addAppender
   * @purpose  Base class
   */
  class LogAppender extends Object {

    /**
     * Append data
     *
     * @model   abstract
     * @access  public
     * @param   mixed* args
     */ 
    public function append() { }
 
    /**
     * Finalize this appender. This method is called when the logger
     * is shut down. Does nothing in this default implementation.
     *
     * @access  public
     */   
    public function finalize() { }
    
    /**
     * Retrieve a readable representation of a variable
     *
     * @access  protected
     * @param   mixed var
     * @return  string
     */
    public function varSource($var) {
      return is_string($var) ? $var : xp::stringOf($var);
    }
  }
?>
