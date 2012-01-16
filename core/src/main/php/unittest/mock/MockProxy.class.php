<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.reflect.InvocationHandler',
    'lang.reflect.Proxy',
    'unittest.mock.RecordState',
    'unittest.mock.ReplayState',
    'unittest.mock.Expectation',
    'unittest.mock.ExpectationViolationException',
    'util.Hashmap'
  );

  /**
   * A mock proxy.
   *
   */
  class MockProxy extends Object implements InvocationHandler {
    private
      $mockState    = NULL,
      $expectionMap = NULL,
      $properties   = NULL;

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->expectionMap= new Hashmap();
      $this->properties= new Hashmap();
      $this->mockState= new RecordState($this->expectionMap);
    }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   var[] args an array of arguments
     * @return  var
     */
    public function invoke($proxy, $method, $args) {
      switch ($method) {
        case '_replayMock':
          return $this->replay();
        case '_isMockRecording':
          return $this->isRecording();
        case '_isMockReplaying':
          return $this->isReplaying();
        case '_verifyMock':
          return $this->verifyMock();
      }
      
      return $this->mockState->handleInvocation($method, $args);
    }

    /**
     * Indicates whether this proxy is in recoding state
     *
     * @return  bool
     */
    public function isRecording() {
       return $this->mockState instanceof RecordState;
    }

    /**
     * Indicates whether this proxy is in replaying state
     *
     * @return  bool
     */
    public function isReplaying() {
      return !$this->isRecording();
    }

    /**
     * Switches state to replay mode
     *
     */
    public function replay() {
      $this->mockState= new ReplayState($this->expectionMap, $this->properties);
    }

    /**
     * Verifies the mock expectations.
     *
     * @throws  unittest.mock.ExpectationViolationException in case verification fails
     */
    public function verifyMock() {
      foreach ($this->expectionMap->keys() as $method) {
        $expectationList= $this->expectionMap->get($method);
        foreach ($expectationList->getExpectations() as $exp) {
          if ($exp->getActualCalls() !== 0) continue;
          throw new ExpectationViolationException($this->constructViolationMessage($exp));
        }
      }
    }

    /**
     * Creates the violation message
     *
     * @param   unittest.mock.Expectation exp
     * @return  string
     */
    private function constructViolationMessage($exp) {
      $msg= 'Method '.$exp->getMethodName().'. ';
      if ($exp->getRepeat()> $exp->getActualCalls()) {
        $msg= 'Expectation not met for "'.$exp->getMethodName().'". ';
      }

      $msg.= 'expected#: '.(-1 === $exp->getRepeat() ? 1 : $exp->getRepeat());
      $msg.= ' called#: '.$exp->getActualCalls();

      return $msg;
    }
  }
?>
