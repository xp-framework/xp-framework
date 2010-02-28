<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.regex.MatchResult');

  /**
   * Matcher interface
   *
   * @see      xp://text.regex.Scanner
   * @see      xp://text.regex.Pattern
   */
  interface Matcher {

    /**
     * Checks whether a given string matches
     *
     * @param   string str
     * @return  bool
     */
    public function matches($str);

    /**
     * Returns match results
     *
     * @param   string str
     * @return  text.regex.MatchResult
     */
    public function match($str);
  }
?>
