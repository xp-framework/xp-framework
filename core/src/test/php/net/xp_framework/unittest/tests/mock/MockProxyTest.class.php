<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.mock.MockProxy',
       'unittest.mock.Mockery',
       'lang.Type',
       'lang.reflect.Proxy',
       'net.xp_framework.unittest.tests.mock.IEmptyInterface',
       'net.xp_framework.unittest.tests.mock.IComplexInterface'
  );

  /**
   * A proxy derivitive which implements additional mock behaviour definition
   * and validation.
   *
   * @see      xp://unittest.mock.MockProxy
   * @purpose  Unit Test
   */
  class MockProxyTest extends TestCase {

    private $sut=null;
    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->sut=new MockProxy();
    }

    /**
     * Can create.
     */
    #[@test]
    public function canCreate() {
      new MockProxy(new Mockery());
    }
    
    /**
     * The mock proxy should provide information obout its state
     */
    #[@test]
    public function canCallIsRecording() {
      $this->sut->isRecording();
    }
    /**
     * The mock is in recording state initially.
     */
    #[@test]
    public function mockIsInRecordingStateInitially() {
      $this->assertTrue($this->sut->isRecording());
    }

    /**
     * Can call invoke
     */
    #[@test]
    public function canCallInvoke() {
      $this->sut->invoke(null, 'foo', null);
    }

    /**
     * Invoke returns some object
     */
    #[@test]
    public function invokeReturnsObject() {
      $this->assertObject($this->sut->invoke(null, 'foo', null));
    }

    /**
     * Can call replay
     */
    #[@test]
    public function canCallReplay() {
      $this->sut->replay();
    }

    /**
     * Can call is Replaying method
     */
    #[@test]
    public function canCallIsReplaying() {
      $this->sut->isReplaying();
    }

    /**
     * Not in replay state initially
     */
    #[@test]
    public function notInReplayStateInitially() {
      $this->assertFalse($this->sut->isReplaying());
    }
    
    /**
     * State changes from recording to replay after call to replay();
     */
    #[@test]
    public function stateChangesAfterReplayCall() {
        $this->assertTrue($this->sut->isRecording());
        $this->assertFalse($this->sut->isReplaying());
        $this->sut->replay();
        $this->assertFalse($this->sut->isRecording());
        $this->assertTrue($this->sut->isReplaying());
    }

    /**
     * It should be always safe to call replay. Even if already in replay mode
     */
    #[@test]
    public function callingReplayTwice_stateShouldNotChange() {
      $this->sut->invoke(null, 'foo', null)->returns('foo1');
      $this->sut->invoke(null, 'foo', null)->returns('foo2');
      $this->sut->invoke(null, 'bar', null)->returns('bar');
      $this->sut->replay();

      $this->assertEquals('foo1', $this->sut->invoke(null, 'foo', null));
      $this->assertEquals('bar', $this->sut->invoke(null, 'bar', null));

      $this->sut->replay(); //should not start over
      $this->assertEquals('foo2', $this->sut->invoke(null, 'foo', null));
      $this->assertEquals(null, $this->sut->invoke(null, 'bar', null));
    }
  }
?>
