<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.mock.arguments.IArgumentMatcher');

  /**
   * Argument matcher, that uses preg_match for matching.
   *
   */
  class PatternMatcher extends Object implements IArgumentMatcher {
    private 
      $pattern= NULL;

    /**
     * Constructor
     *
     * @param string pattern
     */
    public function __construct($pattern) {
      $this->pattern= $pattern;
    }

    /**
     * Matches the pattern against the value.
     * 
     * @param   string value
     * @return  bool
     */
    public function matches($value) {
      return preg_match($this->pattern, $value) === 1;
    }
  }
?>
