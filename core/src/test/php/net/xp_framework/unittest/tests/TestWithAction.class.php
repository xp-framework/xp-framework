<?php namespace net\xp_framework\unittest\tests;

/**
 * This class is used in the TestActionTest 
 */
#[@action(new RecordActionInvocation('run'))]
class TestWithAction extends \unittest\TestCase {
  public $run= array();

  #[@test]
  public function one() {
    $this->run[]= 'one';
  }

  #[@test]
  public function two() {
    $this->run[]= 'two';
  }
}
