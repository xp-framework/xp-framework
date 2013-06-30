<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * This class is used in the TestActionTest 
   */
  #[@action('net.xp_framework.unittest.tests.RecordActionInvocation')]
  class TestWithAction extends TestCase {
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
?>
