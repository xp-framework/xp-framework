<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'unittest.mock.Mockery',
    'lang.reflect.Proxy',
    'net.xp_framework.unittest.tests.mock.IEmptyInterface',
    'net.xp_framework.unittest.tests.mock.IComplexInterface',
    'net.xp_framework.unittest.tests.mock.PartiallyImplementedAbstractDummy',
    'unittest.mock.ExpectationViolationException'
  );

  /**
   * Class for automaticly stubbing interfaces
   *
   * @see      xp://unittest.mock.Mockery
   * @purpose  Unit Test
   */
  class MockeryTest extends TestCase {

    private $fixture=null;
    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->fixture=new Mockery();
    }
      
    /**
     * Can create.
     */
    #[@test]
    public function canCreate() {
      new Mockery();
    }
    /**
     * Can create mock for empty interface
     */
    #[@test]
    public function canCreateMockForEmptyInterface() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IEmptyInterface');
      $this->assertInstanceOf('net.xp_framework.unittest.tests.mock.IEmptyInterface', $object);
    }

     /**
     * Can create mock for non-empty interface
     */
    #[@test]
    public function canCreateMockForComplexInterface() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $this->assertInstanceOf('net.xp_framework.unittest.tests.mock.IComplexInterface', $object);
    }

    /**
     * Can create mock for non-empty interface
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function cannotCreateMockForUnknownTypes() {
      $this->fixture->createMock('foooooo.Unknown');
    }

    /**
     * Can create mock for non-empty interface
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotCreateMockForNonXPClassTypes() {
      $this->fixture->createMock('string');
    }

    /**
     * Can call replay
     */
    #[@test]
    public function canCallReplay() {
      $mock= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $mock->_replayMock();
    }

    /**
     * Can call interface methods
     */
    #[@test]
    public function canCallInterfaceMethods() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object->foo();
    }

    /**
     * Can call returns() on mocked object
     */
    #[@test]
    public function canCallReturnsFluently() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object->foo()->returns(null);
    }

    /**
     * Defined value returned in replay mode
     */
    #[@test]
    public function canDefineReturnValue() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $return = new Object();
      $object->foo()->returns($return);

      $object->_replayMock();
      $this->assertTrue($object->foo()=== $return);
    }

    /**
     * If no expectations are left, null is returned
     */
    #[@test]
    public function missingExpectationLeadsToNull() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $object->foo()->returns(new Object());

      $object->_replayMock();
      $this->assertInstanceOf('lang.Object', $object->foo());
      $this->assertNull($object->foo());
      $this->assertNull($object->foo());
      $this->assertNull($object->foo());
    }
    
    /**
     * If no expectations are left, null is returned
     */
    #[@test]
    public function recordedReturnsAreInCorrectOrder() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $return1="foo";
      $return2="bar";
      $return3="baz";
      
      $object->foo()->returns($return1);
      $object->foo()->returns($return2);
      $object->foo()->returns($return3);
      $object->_replayMock();
      
      $this->assertEquals($return1, $object->foo());
      $this->assertEquals($return2, $object->foo());
      $this->assertEquals($return3, $object->foo());
      $this->assertNull($object->foo());

    }
    
    /**
     * Mockery has replayAll method                       
     */
    #[@test]
    public function canCallReplayAll() {
      $this->fixture->replayAll();
    }
    
    /**
     * All mocks created by one mockery are set to replay mode when
     * replayAll is called.
     */
    #[@test]
    public function replayAllSetsAllMocksInReplayMode() {
      $object1=$this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object2=$this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object3=$this->fixture->createMock('net.xp_framework.unittest.tests.mock.IEmptyInterface');
     
      $this->assertTrue($object1->_isMockRecording());
      $this->assertTrue($object2->_isMockRecording());
      $this->assertTrue($object3->_isMockRecording());

      $this->fixture->replayAll();
      
      $this->assertTrue($object1->_isMockReplaying());
      $this->assertTrue($object2->_isMockReplaying());
      $this->assertTrue($object3->_isMockReplaying());
    }
    
    /**
     * "Partial" mocks are also possible.
     */
    #[@test]
    public function can_createMock_fromAbstractClass() {
      $obj= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.PartiallyImplementedAbstractDummy');
      $this->assertInstanceOf('net.xp_framework.unittest.tests.mock.PartiallyImplementedAbstractDummy', $obj);
    }
    
    /**
     * Abstract methods should be implemented (and delegated to the handler).
     */
    #[@test]
    public function abstractMethodAreMocked() {
      $obj= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.PartiallyImplementedAbstractDummy');

      $baz_expect='BAAAAZ!';
      $obj->baz(null)->returns($baz_expect);

      $bar_expect='BAAARRR';
      $obj->bar(null, null)->returns($bar_expect);

      $obj->_replayMock();

      $this->assertEquals($baz_expect, $obj->baz(null));
      $this->assertEquals($bar_expect, $obj->bar(null, null));
    }

    /**
     * The concretely implemented methods should not be mocked if
     * overrideAll is unset.
     */
    #[@test]
    public function concreteMethodsNotMocked() {
      $obj= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.PartiallyImplementedAbstractDummy');

      $this->assertEquals('IComplexInterface.foo', $obj->foo());
    }
    /**
     *
     */
    #[@test]
    public function concreteClassesMocked_whenSpecifiedSo() {
      $obj= $this->fixture->createMock(
              'net.xp_framework.unittest.tests.mock.PartiallyImplementedAbstractDummy',
              true);

      $foo_expect='fooooo';
      $obj->foo()->returns($foo_expect);
      $obj->_replayMock();

      $this->assertEquals($foo_expect, $obj->foo());
    }

    /**
     * can define return object for two calls with repeat
     */
    #[@test]
    public function canCallRepeat() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $expected= "myFooReturn";
      $object->foo()->returns($expected)->repeat(2);
      $this->fixture->replayAll();

      $this->assertEquals($expected, $object->foo());
      $this->assertEquals($expected, $object->foo());
      $this->assertNull($object->foo());
    }


    /**
     * can define return object for two calls with repeat
     */
    #[@test]
    public function canCallRepeatAny() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $expected= "myFooReturn";
      $object->foo()->returns($expected)->repeatAny();
      $this->fixture->replayAll();

      $this->assertEquals($expected, $object->foo());
      $this->assertEquals($expected, $object->foo());
      $this->assertEquals($expected, $object->foo());
      $this->assertEquals($expected, $object->foo());
      $this->assertEquals($expected, $object->foo());
      $this->assertEquals($expected, $object->foo());
    }

    /**
     * when recording calls, the arguments should be considered.
     */
    #[@test]
    public function method_arguments_should_be_considered_in_recodring() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $conf1Expect='c1';
      $conf2Expect='c2';
      $object->bar('X', 'Conf1')->returns($conf1Expect);
      $object->bar('X', 'Conf2')->returns($conf2Expect);
      $this->fixture->replayAll();

      $this->assertEquals($conf2Expect, $object->bar('X', 'Conf2'));
      $this->assertEquals($conf1Expect, $object->bar('X', 'Conf1'));
    }

    /**
     * It should be possible to define expectations for any arguments.
     */
    #[@test]
    public function arg_any_accepts_all_arguments() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $expect='c1';
      $object->bar(Arg::any(), Arg::any())->returns($expect)->repeatAny();
      $this->fixture->replayAll();

      $this->assertEquals($expect, $object->bar(null, null));
      $this->assertEquals($expect, $object->bar(34, "foo"));
      $this->assertEquals($expect, $object->bar(23.0, new Object));
    }

    /**
     * Unexpected calls should fail, when _verifyMock is called.
     */
    #[@test, @expect('unittest.mock.ExpectationViolationException')]
    public function unexpected_calls_should_fail_on_mock_object_verification() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $object->foo(); //expect a call to foo
      $this->fixture->replayAll();

      $object->_verifyMock(); // expected call to foo missing -> fail
    }

     /**
     * verifyAll() verifies all mocks of that mockery.
     */
    #[@test]
    public function verfyAll_should_verfiy_all_mocks__positive_case() {
      $object1= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object2= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object3= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object4= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');

      $object1->foo();
      $object2->foo();
      $object3->foo();
      $object4->foo();
      $this->fixture->replayAll();

      $object1->foo();
      $object2->foo();
      $object3->foo();
      $object4->foo();
      $this->fixture->verifyAll();
    }

    /**
     * verifyAll() verifies all mocks of that mockery.
     */
    #[@test, @expect('unittest.mock.ExpectationViolationException')]
    public function verfyAll_should_verfiy_all_mocks__negative_case() {
      $object1= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object2= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object3= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object4= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      
      $object1->foo();
      $object2->foo();
      $object3->foo();
      $object4->foo();
      $this->fixture->replayAll();

      $object1->foo();
      $object2->foo();
      $object3->foo();
      //missed call on $object4->foo();
      $this->fixture->verifyAll();
    }

    /**
     * It should be possible to define an exception that is thrown on a call
     */
    #[@test]
    public function canCall_throws_on_mock_object() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $object->foo()->throws(new XPException('foo'));
    }

    /**
     * When a expectation with "throws" is defined on a mock, then this exception
     * should be thrown when the expected method is called in replay mode.
     */
    #[@test]
    public function mock_with_throws_should_throw_the_specified_exception() {
      $object= $this->fixture->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $expected= new IllegalStateException('foo');
      $object->foo()->throws($expected);

      $this->fixture->replayAll();
      try { $object->foo(); }
      catch(IllegalStateException $actual) {
        $this->assertEquals($expected, $actual);
        return;
      }

      $this->fail('No exception thrown.', null, $expected);
    }
  }
?>
