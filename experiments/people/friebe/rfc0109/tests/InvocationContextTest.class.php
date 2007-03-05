<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.invoke.InvocationContext'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class InvocationContextTest extends TestCase {
  
    /**
     * Invocation target
     *
     * @return  util.invoke.Call
     */
    public function invocationTarget() {
      return InvocationContext::getCaller();
    }

    /**
     * Invocation target for a static call
     *
     * @return  util.invoke.Invocation
     */
    public static function staticInvocationTarget() {
      return InvocationContext::getCaller();
    }
    
    /**
     * Invoke the invocationTarget() method via constructor
     *
     * @return  util.invoke.Invocation
     */
    protected function invokeViaConstructor() {
      return newinstance('lang.Object', array($this, 'invocationTarget'), '{
        public $invocation;

        public function __construct($instance, $method) {
          $this->invocation= $instance->{$method}();
        }
      }')->invocation;
    }
  
    /**
     * Test invocation
     *
     */
    #[@test]
    public function invocation() {
      $caller= $this->invocationTarget();
      $this->assertEquals($this->getClass(), $caller->getCallingClass());
      $this->assertEquals($this->getClass()->getMethod(__FUNCTION__), $caller->getCallingMethod());
      $this->assertEquals(0, $caller->numArguments());
    }

    /**
     * Test static invocation
     *
     */
    #[@test]
    public function staticInvocation() {
      $caller= self::staticInvocationTarget();
      $this->assertEquals($this->getClass(), $caller->getCallingClass());
      $this->assertEquals($this->getClass()->getMethod(__FUNCTION__), $caller->getCallingMethod());
      $this->assertEquals(0, $caller->numArguments());
    }

    /**
     * Test invocation via constructor
     *
     */
    #[@test]
    public function constructorInvocation() {
      $caller= $this->invokeViaConstructor();
      $this->assertSubclass($caller->getCallingClass(), 'lang.Object');
      $this->assertClass($caller->getCallingMethod(), 'lang.reflect.Constructor');
      // $this->assertEquals(0, $caller->numArguments()); XXX FIXME why is #args= 2? XXX
    }

    /**
     * Test invocation via lambda function
     *
     */
    #[@test]
    public function lambdaInvocation() {
      $inv= create_function('$instance, $method', 'return $instance->{$method}();');
      $caller= $inv($this, 'invocationTarget');
      $this->assertSubclass($caller->getCallingClass(), 'lang.Object');
      $this->assertSubclass($caller->getCallingMethod(), 'lang.reflect.Routine');
      $this->assertEquals(2, $caller->numArguments());
    }
  }
?>
