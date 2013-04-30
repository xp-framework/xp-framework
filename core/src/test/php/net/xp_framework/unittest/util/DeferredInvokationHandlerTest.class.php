<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.AbstractDeferredInvokationHandler'
  );

  /**
   * TestCase for AbstractDeferredInvokationHandler
   */
  class DeferredInvokationHandlerTest extends TestCase {

    /**
     * Test
     */
    #[@test]
    public function echo_runnable_invokation() {
      $handler= newinstance('util.AbstractDeferredInvokationHandler', array(), '{
        public function initialize() { 
          return newinstance("lang.Runnable", array(), "{
            public function run() {
              return func_get_args();
            }
          }");
        }
      }');
      $args= array(1, 2, 'Test');
      $this->assertEquals($args, $handler->invoke($this, 'run', $args));
    }

    /**
     * Test
     */
    #[@test, @expect(class = 'lang.XPException', withMessage= 'Test')]
    public function throwing_runnable_invokation() {
      $handler= newinstance('util.AbstractDeferredInvokationHandler', array(), '{
        public function initialize() { 
          return newinstance("lang.Runnable", array(), "{
            public function run() {
              throw new XPException(func_get_arg(0));
            }
          }");
        }
      }');
      $handler->invoke($this, 'run', array('Test'));
    }

    /**
     * Test
     */
    #[@test, @expect(class = 'util.DeferredInitializationException', withMessage= 'run')]
    public function initialize_returns_null() {
      $handler= newinstance('util.AbstractDeferredInvokationHandler', array(), '{
        public function initialize() { 
          return NULL;
        }
      }');
      $handler->invoke($this, 'run', array());
    }

    /**
     * Test
     */
    #[@test, @expect(class = 'util.DeferredInitializationException', withMessage= 'run')]
    public function initialize_throws_exception() {
      $handler= newinstance('util.AbstractDeferredInvokationHandler', array(), '{
        public function initialize() { 
          throw new IllegalStateException("Cannot initialize yet");
        }
      }');
      $handler->invoke($this, 'run', array());
    }

    /**
     * Test
     */
    #[@test]
    public function initialize_not_called_again_after_success() {
      $handler= newinstance('util.AbstractDeferredInvokationHandler', array(), '{
        private $actions;
        public function __construct() {
          $this->actions= array(
            function() { return newinstance("lang.Runnable", array(), "{
              public function run() { return TRUE; }
            }"); },
            function() { throw new IllegalStateException("Initialization called again"); },
          );
        }
        public function initialize() {
          return call_user_func(array_shift($this->actions));
        }
      }');
      $this->assertEquals(TRUE, $handler->invoke($this, 'run', array()));
      $this->assertEquals(TRUE, $handler->invoke($this, 'run', array()));
    }  

    /**
     * Test
     */
    #[@test]
    public function initialize_called_again_after_failure() {
      $handler= newinstance('util.AbstractDeferredInvokationHandler', array(), '{
        private $actions;
        public function __construct() {
          $this->actions= array(
            function() { throw new IllegalStateException("Error initializing"); },
            function() { return newinstance("lang.Runnable", array(), "{
              public function run() { return TRUE; }
            }"); }
          );
        }
        public function initialize() {
          return call_user_func(array_shift($this->actions));
        }
      }');
      try {
        $handler->invoke($this, 'run', array());
      } catch (DeferredInitializationException $expected) {
        // OK
      }
      $this->assertEquals(TRUE, $handler->invoke($this, 'run', array()));
    }
  }
?>
