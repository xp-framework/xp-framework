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
     * @return  util.invoke.Call
     */
    public static function staticInvocationTarget() {
      return InvocationContext::getCaller();
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
  }
?>
