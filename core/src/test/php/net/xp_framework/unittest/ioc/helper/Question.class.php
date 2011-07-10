<?php
/* This class is part of the XP framework
 *
 * $Id: Goodyear.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  /**
   * @purpose  Helper class for test cases.
   */
  class Question extends Object {
    private $answer;

    #[@inject, @named('answer')]
    public function setAnswer($answer) {
      $this->answer = $answer;
    }

    public function getAnswer() {
      return $this->answer;
    }
  }
?>
