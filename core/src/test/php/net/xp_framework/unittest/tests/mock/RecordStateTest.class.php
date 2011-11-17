<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.mock.RecordState',
       'util.Hashmap');

  /**
   * TODO: Description
   *
   * @see      xp://unittest.mock.RecordState
   * @purpose  Unit Test
   */
  class RecordStateTest extends TestCase {

    private 
      $sut= null,
      $expectationMap= null;
    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->expectationMap= new Hashmap();
      $this->sut=new RecordState($this->expectationMap);
    }
      
    /**
     * Cannot create without valid Hasmap.
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectationMapRequiredOnCreate() {
      new RecordState(null);
    }
    
    /**
     * Can create with valid hasmap.
     */
    #[@test]
    public function canCreate() {
      new RecordState(new Hashmap());
    }
    /**
     * Can call handleInvocation.
     */
    #[@test]
    public function canHandleInvocation() {
      $this->sut->handleInvocation(null, null);
    }

    /**
     * a new expectation is created when calling handleInvocation
     */
    #[@test]
    public function newExpectationCreatedOnHandleInvocation() {
      $this->sut->handleInvocation('foo', null);
      $this->assertEquals(1, $this->expectationMap->size());
      $expectationList= $this->expectationMap->get('foo');
      $this->assertInstanceOf('unittest.mock.ExpectationList', $expectationList);
      $this->assertInstanceOf('unittest.mock.Expectation', $expectationList->getNext(array()));
    }

    /**
     * a new expectation is created when calling handleInvocation
     */
    #[@test]
    public function newExpectationCreatedOnHandleInvocation_twoDifferentMethods() {
      $this->sut->handleInvocation('foo', null);
      $this->sut->handleInvocation('bar', null);
      $this->assertInstanceOf('unittest.mock.Expectation', $this->expectationMap->get('foo')->getNext(array()));
      $this->assertInstanceOf('unittest.mock.Expectation', $this->expectationMap->get('bar')->getNext(array()));
    }

    /**
     * a new expectation is created when calling handleInvocation
     */
    #[@test]
    public function newExpectationCreatedOn_EACH_HandleInvocationCall() {
      $this->sut->handleInvocation('foo', null);
      $this->sut->handleInvocation('foo', null);
      $expectationList= $this->expectationMap->get('foo');

      $this->assertInstanceOf('unittest.mock.ExpectationList', $expectationList);
      $this->assertInstanceOf('unittest.mock.Expectation', $expectationList->getNext(array()));
      $this->assertInstanceOf('unittest.mock.Expectation', $expectationList->getNext(array()));

    }

    /**
     * The expectations arguments should be set in handleInvocation.
     */
    #[@test]
    public function method_call_should_set_arguments() {
      $args= array('1', 2, 3.0);
      $this->sut->handleInvocation('foo', $args);

      $expectationList= $this->expectationMap->get('foo');
      $expectedExpectaton= $expectationList->getNext($args);
      $this->assertObject($expectedExpectaton);
    }
  }
?>
