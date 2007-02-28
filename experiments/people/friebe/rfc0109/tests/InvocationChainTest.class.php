<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.invoke.InvocationChain',
    'util.invoke.InvocationInterceptor'
  );

  /**
   * TestCase
   *
   * @see      xp://de.schlund.intranet.search.interceptor.InvocationChain
   * @purpose  purpose
   */
  class InvocationChainTest extends TestCase {
    protected
      $chain  = NULL,
      $target = NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->chain= new InvocationChain();
      $this->target= $this->getClass()->getMethod('targetMethod');
    }
    
    /**
     * Chain target method
     *
     * @param   mixed param
     * @return  mixed
     */
    public function targetMethod($param) {
      return $param;
    }
    
    /**
     * Test a chain without additional calls.
     *
     */
    #[@test]
    public function emptyChain() {
      $this->assertEquals(
        'Hello', 
        $this->chain->invoke($this, $this->target, array('Hello'))
      );
    }

    /**
     * Test a chain with a NOOP interceptor 
     *
     */
    #[@test]
    public function noopInterceptor() {
      $this->chain->addInterceptor(newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          return $chain->proceed();
        }
      }'));
      $this->assertEquals(
        'Hello', 
        $this->chain->invoke($this, $this->target, array('Hello'))
      );
    }

    /**
     * Test a chain with an interceptor that modifies the result
     *
     */
    #[@test]
    public function modifyingInterceptor() {
      $this->chain->addInterceptor(newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          return $chain->proceed()."X";
        }
      }'));
      $this->assertEquals(
        'HelloX', 
        $this->chain->invoke($this, $this->target, array('Hello'))
      );
    }

    /**
     * Test a chain with two interceptors modifying the result
     *
     */
    #[@test]
    public function modifyingInterceptors() {
      $interceptor= newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          return $chain->proceed()."X";
        }
      }');
      $this->chain->addInterceptor($interceptor);
      $this->chain->addInterceptor($interceptor);
      $this->assertEquals(
        'HelloXX', 
        $this->chain->invoke($this, $this->target, array('Hello'))
      );
    }

    /**
     * Test chain invocations stop at the first exception
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function chainStopsAtFirstException() {
      $this->chain->addInterceptor(newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          throw new IllegalArgumentException("STOP");
        }
      }'));
      $this->chain->addInterceptor(newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          throw new IllegalStateException("Chain has not stopped");
        }
      }'));
      $this->chain->invoke($this, $this->target, array());
    }

    /**
     * Test chain around-invoke semantics
     *
     */
    #[@test]
    public function aroundInvoke() {
      $this->chain->addInterceptor(newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          try {
            return $chain->proceed();
          } catch (IllegalArgumentException $e) {
            return "W@".$e->getMessage();
          }
        }
      }'));
      $this->chain->addInterceptor(newinstance('InvocationInterceptor', array(), '{
        public function invoke(InvocationChain $chain) {
          throw new IllegalArgumentException("AroundInvoke");
        }
      }'));
      $this->assertEquals(
        'W@AroundInvoke', 
        $this->chain->invoke($this, $this->target, array())
      );
    }

    /**
     * Test addInterceptor() throws an exception when passed an illegal argument
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalArgument() {
      $this->chain->addInterceptor(new Object());
    }
  }
?>
