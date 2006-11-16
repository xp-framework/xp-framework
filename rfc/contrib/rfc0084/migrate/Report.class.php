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
      $messages= array(),
      $packages= array();
    
    /**
     * Add messages for a given file
     *
     * @access  public
     * @param   &io.File file
     * @param   array<string, mixed[]> messages
     */
    function add(&$f, $messages) { 
      $this->messages[$f->getURI()]= $messages;
      foreach (array_keys($messages) as $package) {
        isset($this->packages[$package]) ? $this->packages[$package]++ : $this->packages[$package]= 1;
      }
    }
    
    /**
     * Return whether there's nothing to report
     *
     * @access  public
     * @return  bool
     */
    function isEmpty() {
      return empty($this->messages);
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
