<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.mock.MethodOptions');

  /**
   * Tests for the MethodOptions class.
   */
  class MethodOptionsTest extends TestCase {

    private $sut=null;
    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->sut=new MethodOptions(new Expectation());
    }
      
    /**
     * Can create.
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectationRequiredOnCreate() {
      new MethodOptions(null);
    }

    /**
     * Can call returns.
     */
    #[@test]
    public function canCallReturns() {
      $this->sut->returns(null);
    }

    /**
     * When returns is called, the expectation's return value should be set too.
     */
    #[@test]
    public function returns_valueSetInExpectation() {
      $expectation=new Expectation();
      $sut= new MethodOptions($expectation);
      $expected=new Object();

      $sut->returns($expected);

      $this->assertEquals($expected, $expectation->getReturn());
    }

    /**
     * when throws is used, the expectations exception property should be set
     * to the passed value.
     */
    #[@test]
    public function throws_sets_the_exception_property_of_the_expectation() {
      $expectation=new Expectation();
      $sut= new MethodOptions($expectation);
      $expected=new XPException('foo');

      $sut->throws($expected);

      $this->assertEquals($expected, $expectation->getException());
    }
  }
?>
