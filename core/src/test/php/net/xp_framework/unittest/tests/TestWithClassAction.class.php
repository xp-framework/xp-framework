<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * This class is used in the TestClassActionTest 
   */
  #[@action('net.xp_framework.unittest.tests.RecordClassActionInvocation')]
  class TestWithClassAction extends TestCase {
    public static $run= array();

    #[@test]
    public function fixture() {
      self::$run[]= 'test';
    }
  }
?>
