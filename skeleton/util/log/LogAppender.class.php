<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for appenders
   *
   * @deprecated  Use util.log.Appender instead
   * @see      xp://util.log.LogCategory#addAppender
   * @purpose  Base class
   */
  abstract class LogAppender extends Object {

    /**
     * Append data
     *
     * @param   var* args
     */ 
    public abstract function append();
 
    /**
     * Finalize this appender. This method is called when the logger
     * is shut down. Does nothing in this default implementation.
     *
     */   
    public function finalize() { }
    
    /**
     * Retrieve a readable representation of a variable
     *
     * @param   var var
     * @return  string
     */
    protected function varSource($var) {
      return is_string($var) ? $var : xp::stringOf($var);
    }
  }
?>
