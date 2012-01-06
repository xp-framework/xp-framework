<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.Expectation',
    'unittest.mock.arguments.Arg'
  );

  /**
   * Test cases for the Expectation class
   *
   * @see   xp://unittest.mock.Expectation
   */
  class ExpectationTest extends TestCase {
    private $sut= NULL;

    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->sut= new Expectation('method');
    }

    /**
     * Can create.
     *
     */
    #[@test]
    public function canCreate() {
      new Expectation('method');
    }

    /**
     * By default the return value is NULL
     *
     */
    #[@test]
    public function returnValue_isNull_byDefault() {
      $this->assertNull($this->sut->getReturn());
    }
    
    /**
     * The return value can be set
     *
     */
    #[@test]
    public function returnValue_canSetGet() {
      $this->sut->setReturn('foo');
      $this->assertEquals('foo', $this->sut->getReturn());
    }
    
    /**
     * The repeat count is 0 by default
     *
     */
    #[@test]
    public function repeat_isMinus1_byDefault() {
      $this->assertEquals(-1, $this->sut->getRepeat());
    }

    /**
     * The repeat count can be set
     *
     */
    #[@test]
    public function repeat_canSetGet() {
      $this->sut->setRepeat(5);
      $this->assertEquals(5, $this->sut->getRepeat());
    }

    /**
     * The actual call count is 0 by default
     *
     */
    #[@test]
    public function actualCalls_is0_byDefault() {
      $this->assertEquals(0, $this->sut->getActualCalls());
    }

    /**
     * The repeat count can be set
     *
     */
    #[@test]
    public function incActualCalls_increasesBy1() {
      $this->sut->incActualCalls();
      $this->assertEquals(1, $this->sut->getActualCalls());
    }

    /**
     * CanRepeat is TRUE by default
     *
     */
    #[@test]
    public function canRepeat_isTrueOnce_ByDefault() {
      $this->sut->setRepeat(1);
      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
      $this->assertFalse($this->sut->canRepeat());
    }

    /**
     * CanRepeat should be TRUE if repeat is set to -1,
     * even after incActualCalls
     *
     */
    #[@test]
    public function canRepeat_isTrue_withRepeatMinus1() {
      $this->sut->setRepeat(-1);

      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
    }

    /**
     * CanRepeat with repeat == 1 returns 2 times TRUE and then FALSE
     *
     */
    #[@test]
    public function canRepeat_withNumericRepeat2_TrueTwice() {
      $this->sut->setRepeat(2);
      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
      $this->assertTrue($this->sut->canRepeat());
      $this->sut->incActualCalls();
      $this->assertFalse($this->sut->canRepeat());
    }

    /**
     * set/getArguments
     *
     */
    #[@test]
    public function setArguments_should_set_arguments() {
      $expected= array('foo', 'bar', 5);
      $this->sut->setArguments($expected);
      $actual= $this->sut->getArguments();
      $this->assertEquals($expected, $actual);   
    }

    /**
     * doesMatchArgs exists
     *
     */
    #[@test]
    public function cancall_doesMatchArgs() {
      $this->sut->doesMatchArgs(array());
    }

    /**
     * The number of arguments is relevant for matching.
     *
     */
    #[@test]
    public function argument_count_should_be_considered_when_matching_args() {
      $this->sut->setArguments(array(1, 2));
      $this->assertTrue($this->sut->doesMatchArgs(array(1, 2)));
      $this->assertFalse($this->sut->doesMatchArgs(array()));
      $this->assertFalse($this->sut->doesMatchArgs(array(1)));
      $this->assertFalse($this->sut->doesMatchArgs(array(1, 2, 3)));
    }

    /**
     * Types are relevant for matching arguments.
     *
     */
    #[@test]
    public function doesMatch_should_return_false_on_differentTypes() {
      $this->sut->setArguments(array('1'));

      $this->assertFalse($this->sut->doesMatchArgs(array(1)));
    }

    /**
     * Equality is of course relevant for argument matching.
     *
     */
    #[@test]
    public function doesMatch_should_return_true_if_args_are_equal() {
      $this->sut->setArguments(array('1', 2, 3.0, '4'));

      $this->assertTrue($this->sut->doesMatchArgs(array('1', 2, 3.0, '4')));
    }

    /**
     * Unequal arguments should not match.
     *
     */
    #[@test]
    public function doesMatch_should_return_false_if_args_are_unequal() {
      $this->sut->setArguments(array('1', 2, 3.0, '4'));

      $this->assertFalse($this->sut->doesMatchArgs(array('x', 2, 3.0, '4')));
    }

    /**
     * NULL is also a valid argument.
     *
     */
    #[@test]
    public function doesMatch_should_work_with_null() {
      $this->sut->setArguments(array(NULL, NULL));

      $this->assertTrue($this->sut->doesMatchArgs(array(NULL, NULL)));
    }

    /**
     * Arg::any() should work for any arguments
     *
     */
    #[@test]
    public function doesMatch_should_work_with_generic_AnyMatcher() {
      $this->sut->setArguments(array(Arg::any()));

      $this->assertTrue($this->sut->doesMatchArgs(array(NULL)));
      $this->assertTrue($this->sut->doesMatchArgs(array('test')));
      $this->assertTrue($this->sut->doesMatchArgs(array(42)));
      $this->assertTrue($this->sut->doesMatchArgs(array(new Object())));
    }

    /**
     * Sets the exception that is to be thrown on execution
     *
     */
    #[@test]
    public function setExceptions_should_set_exception() {
      $expected= new XPException('foo');
      $this->sut->setException($expected);
      $actual= $this->sut->getException();

      $this->assertEquals($expected, $actual);
    }
  }
?>
