<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'lang.StackTraceElement');

  /**
   * Tests for the StackTraceElement class
   */
  class StackTraceElementTest extends TestCase {
    const NEW_FIXTURE_METHOD = '  at net.xp_framework.unittest.core.StackTraceElementTest::newFixtureWith';

    /**
     * Test constructor
     */
    #[@test]
    public function can_create() {
      new StackTraceElement('file', 'class', 'method', 1, array(), 'Message');
    }

    /**
     * Test equals()
     */
    #[@test]
    public function is_equal_to_itself() {
      $a= new StackTraceElement('file', 'class', 'method', 1, array(), 'Message');
      $this->assertEquals($a, $a);
    }

    /**
     * Test equals()
     */
    #[@test]
    public function two_identical_stacktraceelements_are_equal() {
      $a= new StackTraceElement('file', 'class', 'method', 1, array(), 'Message');
      $b= new StackTraceElement('file', 'class', 'method', 1, array(), 'Message');
      $this->assertEquals($a, $b);
    }

    /**
     * Creates a new fixture with args set to the given arguments
     *
     * @param  var[] $args The arguments
     * @return lang.StackTraceElement the fixture
     */
    protected function newFixtureWith($args) {
      return new StackTraceElement('Test.class.php', __CLASS__, __FUNCTION__, 1, $args, 'Test');
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."() [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array())->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_one_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."((0x5)'Hello') [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array('Hello'))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_two_args() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."((0x5)'Hello', 2) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array('Hello', 2))->toString()
      );
    }
  }
?>
