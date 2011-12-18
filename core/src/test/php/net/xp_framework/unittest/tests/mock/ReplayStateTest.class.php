<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.mock.ReplayState',
    'unittest.mock.Expectation',
    'unittest.mock.ExpectationList'
  );

  /**
   * TODO: Description
   *
   * @see      xp://unittest.mock.Replay
   * @purpose  Unit Test
   */
  class ReplayStateTest extends TestCase {

    private 
      $sut=NULL,
      $expectationMap,
      $properties;
    
    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->expectationMap= new Hashmap();
      $this->properties= new Hashmap();
      
      $this->sut= new ReplayState($this->expectationMap, $this->properties);
    }
      
    /**
     * Cannot create without valid Hasmap.
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function expectationMapRequiredOnCreate() {
      new ReplayState(NULL, NULL);
    }
    
    /**
     * Cannot create without valid Hasmap.
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function propertiesRequiredOnCreate() {
      new ReplayState(new Hashmap(), NULL);
    }
    /**
     * Can create with valid hasmap.
     */
    #[@test]
    public function canCreate() {
      new ReplayState(new Hashmap(), new Hashmap());
    }
    
    /**
     * Can call handle invocation.
     */
    #[@test]
    public function canHandleInvocation() {
      $this->sut->handleInvocation(NULL, NULL);
    }
    
    /**
     * if expectation exists, return value is returned                        
     */
    #[@test]
    public function handleInvocation_withExistingExpectation_returnExpectationsReturnValue() {
      $myExpectation= new Expectation('foo');
      $myExpectation->setReturn('foobar');
      
      $expectationsList= new ExpectationList();
      $expectationsList->add($myExpectation);
      
      $this->expectationMap->put('foo', $expectationsList);
      
      $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', NULL));
    }

    /**
     * if no expectations are left, NULL is returned                       
     */
    #[@test]
    public function handleInvocation_missingExpectation_returnsNull() {
      $myExpectation= new Expectation('foo');
      $myExpectation->setReturn('foobar');
      
      $expectationsList= new ExpectationList();    
      $this->expectationMap->put('foo', $expectationsList);
      
      $this->assertNull($this->sut->handleInvocation('foo', NULL));
    }

    /**
     * Repetions work in replay state.
     */
    #[@test]
    public function handleInvocation_ExpectationRepeatedTwice_returnExpectationsReturnValueTwice() {
      $myExpectation= new Expectation('foo');
      $myExpectation->setReturn('foobar');
      $myExpectation->setRepeat(2);

      $expectationsList= new ExpectationList();
      $expectationsList->add($myExpectation);

      $this->expectationMap->put('foo', $expectationsList);

      $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', NULL));
      $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', NULL));
      $this->assertNull($this->sut->handleInvocation('foo', NULL));
    }

    /**

     */
  #[@test]
    public function handleInvocation_should_throw_exception_when_expectation_defines_one() {
      $expected= new XPException('foo');
      $myExpectation= new Expectation('foo');
      $myExpectation->setException($expected);
      $expectationsList= new ExpectationList();
      $expectationsList->add($myExpectation);
      
      $this->expectationMap->put('foo', $expectationsList);

      try { $this->sut->handleInvocation('foo', NULL); }
      catch(XPException $e) {
        $this->assertEquals($expected, $e);
        return;
      }

      $this->fail('Exception not thrown.', NULL, $expect);
    }
  }
?>
