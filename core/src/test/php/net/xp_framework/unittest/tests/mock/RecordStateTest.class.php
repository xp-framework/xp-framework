<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.mock.RecordState',
    'util.Hashmap'
  );

  /**
   * Testcase for RecordState
   *
   * @see   xp://unittest.mock.RecordState
   */
  class RecordStateTest extends TestCase {
    private 
      $sut            = NULL,
      $expectationMap = NULL;
    
    /**
     * Creates the fixture
     *
     */
    public function setUp() {
      $this->expectationMap= new Hashmap();
      $this->sut= new RecordState($this->expectationMap);
    }
      
    /**
     * Cannot create without valid Hasmap.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectationMapRequiredOnCreate() {
      new RecordState(NULL);
    }
    
    /**
     * Can create with valid hasmap.
     *
     */
    #[@test]
    public function canCreate() {
      new RecordState(new Hashmap());
    }

    /**
     *
     * Can call handleInvocation.
     */
    #[@test]
    public function canHandleInvocation() {
      $this->sut->handleInvocation('methodName', NULL);
    }

    /**
     * A new expectation is created when calling handleInvocation
     *
     */
    #[@test]
    public function newExpectationCreatedOnHandleInvocation() {
      $this->sut->handleInvocation('foo', NULL);
      $this->assertEquals(1, $this->expectationMap->size());
      $expectationList= $this->expectationMap->get('foo');
      $this->assertInstanceOf('unittest.mock.ExpectationList', $expectationList);
      $this->assertInstanceOf('unittest.mock.Expectation', $expectationList->getNext(array()));
    }

    /**
     * A new expectation is created when calling handleInvocation
     *
     */
    #[@test]
    public function newExpectationCreatedOnHandleInvocation_twoDifferentMethods() {
      $this->sut->handleInvocation('foo', NULL);
      $this->sut->handleInvocation('bar', NULL);
      $this->assertInstanceOf('unittest.mock.Expectation', $this->expectationMap->get('foo')->getNext(array()));
      $this->assertInstanceOf('unittest.mock.Expectation', $this->expectationMap->get('bar')->getNext(array()));
    }

    /**
     * A new expectation is created when calling handleInvocation
     *
     */
    #[@test]
    public function newExpectationCreatedOn_EACH_HandleInvocationCall() {
      $this->sut->handleInvocation('foo', NULL);
      $this->sut->handleInvocation('foo', NULL);
      $expectationList= $this->expectationMap->get('foo');

      $this->assertInstanceOf('unittest.mock.ExpectationList', $expectationList);
      $this->assertInstanceOf('unittest.mock.Expectation', $expectationList->getNext(array()));
      $this->assertInstanceOf('unittest.mock.Expectation', $expectationList->getNext(array()));
    }

    /**
     * The expectations arguments should be set in handleInvocation.
     *
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
