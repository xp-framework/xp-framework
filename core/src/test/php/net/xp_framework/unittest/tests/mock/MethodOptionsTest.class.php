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
      $this->sut=new MethodOptions(new Expectation(), "method");
    }
      
    /**
     * Test
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectationRequiredOnCreate() {
      new MethodOptions(null, null);
    }

    /**
     * Test
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nameRequiredOnCreate() {
      new MethodOptions(new Expectation(), null);
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
      $sut= new MethodOptions($expectation, "foo");
      $expected=new Object();

      $sut->returns($expected);

      $this->assertEquals($expected, $expectation->getReturn());
    }

    /**
     * Test
     */
    #[@test]
    public function setPropertyBehavior_sets_expectation_to_prop_behavior() {
      $expectation=new Expectation();
      $sut= new MethodOptions($expectation, "setFoo");
      $sut->propertyBehavior();

      $this->assertTrue($expectation->isInPropertyBehavior());
    }
    
    /**
     * when throws is used, the expectations exception property should be set
     * to the passed value.
     */
    #[@test]
    public function throws_sets_the_exception_property_of_the_expectation() {
      $expectation=new Expectation();
      $sut= new MethodOptions($expectation, "foo");
      $expected=new XPException('foo');

      $sut->throws($expected);

      $this->assertEquals($expected, $expectation->getException());
    }
    
    /**
     * Test
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function setPropertyBehavior_throws_an_exception_if_no_setter_or_getter() {
      $expectation=new Expectation();
      $sut= new MethodOptions($expectation, "blabla");
      $sut->propertyBehavior();
    }
  }
?>
