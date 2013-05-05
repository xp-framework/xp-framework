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
    public function to_string_with_array_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(array[3]) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(array(1, 2, 3)))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_empty_array_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(array[0]) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(array()))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_string_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."((0x5)'Hello') [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array('Hello'))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_long_string_arg() {
      $str= str_repeat('*', 0x80);
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."((0x80)'".str_repeat('*', 0x40)."') [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array($str))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_string_with_newline_arg() {
      $str= "Hello\nWorld";
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."((0xb)'Hello') [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array($str))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_string_with_nul_arg() {
      $str= "Hello\0";
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."((0x6)'Hello\\000') [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array($str))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_int_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(6100) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(6100))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_double_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(-1.5) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(-1.5))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_bool_true_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(1) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(TRUE))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_bool_false_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."() [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(FALSE))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_null_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(NULL) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(NULL))->toString()
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_object_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(lang.Object{}) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(new Object()))->toString()
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

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_resource_arg() {
      $fd= fopen(__FILE__, 'r');
      $string= $this->newFixtureWith(array($fd))->toString();
      $fds= (string)$fd;
      fclose($fd);

      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(".$fds.") [line 1 of Test.class.php] Test\n",
        $string
      );
    }

    /**
     * Test toString()
     */
    #[@test]
    public function to_string_with_function_arg() {
      $this->assertEquals(
        self::NEW_FIXTURE_METHOD."(php.Closure{}) [line 1 of Test.class.php] Test\n",
        $this->newFixtureWith(array(function() { }))->toString()
      );
    }
  }
?>
