<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract base class for other reports
   *
   * @purpose  Base class
   */
  class Report extends Object {
    var 
      $messages= array();
    
    /**
     * Add messages for a given file
     *
     * @access  public
     * @param   &io.File file
     * @param   array<string, mixed[]> messages
     */
    function add(&$f, $messages) { 
      $this->messages[$f->getURI()]= $messages;
    }

    /**
     * Summarize this report
     *
     * @model   abstract
     * @access  public
     * @param   &io.collections.IOCollection collection
     * @param   &io.File out
     * @param   array<string, &Rule> rules
     */
    function summarize(&$collection, &$out, $rules) { }

    /**
     * Creates a string representation of this report's type
     *
     * @model   abstract
     * @access  public
     * @return  string
     */
    function toString() { }
  }
?>
