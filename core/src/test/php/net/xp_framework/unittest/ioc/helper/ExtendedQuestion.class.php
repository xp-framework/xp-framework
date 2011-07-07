<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * @purpose  Helper class for test cases.
   */
  class ExtendedQuestion extends Object {
    private $answer;

    #[@Inject]
    public function setAnswer(Answer $answer) {
      $this->answer = $answer;
    }

    public function getAnswer() {
      return $this->answer;
    }
  }
?>
