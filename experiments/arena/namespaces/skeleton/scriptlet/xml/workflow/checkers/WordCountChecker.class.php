<?php
/* This class is part of the XP framework
 *
 * $Id: WordCountChecker.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::checkers;

  uses('scriptlet.xml.workflow.checkers.ParamChecker');

  /**
   * Checks given values for string length
   *
   * Error codes returned are:
   * <ul>
   *   <li>notenough - if the number of words in the given value is less than the lower boundary</li>
   * </ul>
   *
   * @purpose  Checker
   */
  class WordCountChecker extends ParamChecker {
    public
      $minWords = 0;
    
    /**
     * Construct
     *
     * @param   int minWords
     */
    public function __construct($minWords) {
      $this->minWords= $minWords;
    }
    
    /**
     * Check a given value
     *
     * @param   array value
     * @return  string error or NULL on success
     */
    public function check($value) { 
      foreach ($value as $v) {
        if (str_word_count($v) < $this->minWords) return 'notenough';
      }    
    }
  }
?>
