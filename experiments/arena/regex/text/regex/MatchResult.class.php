<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a match result
   *
   * @see      xp://text.regex.Pattern#matches
   * @purpose  Result
   */
  class MatchResult extends Object {
    protected
      $length     = 0,
      $matches    = array();
    
    /**
     * Constructor
     *
     * @param   int length
     * @param   string[][] matches
     */
    public function __construct($length, $matches) {
      $this->length= $length;
      $this->matches= $matches;
    }
    
    /**
     * Return how many matches there where
     *
     * @return  int
     */
    public function length() {
      return $this->length;
    }

    /**
     * Returns all matched groups
     *
     * @return  string[][]
     */
    public function groups() {
      return $this->matches;
    }

    /**
     * Returns the matched group with the specified group offset
     *
     * @param   int offset
     * @return  string[]
     */
    public function group($offset) {
      return $this->matches[$offset];
    }
  }
?>
