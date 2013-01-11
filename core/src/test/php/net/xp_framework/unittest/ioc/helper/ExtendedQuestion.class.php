<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Helper class for test cases.
   */
  class ExtendedQuestion extends Object {
    private $answer;

    #[@inject]
    public function setAnswer(Answer $answer) {
      $this->answer = $answer;
    }

    public function getAnswer() {
      return $this->answer;
    }
  }
?>
