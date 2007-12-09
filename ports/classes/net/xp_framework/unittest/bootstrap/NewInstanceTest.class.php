<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.bootstrap.AbstractBootstrapTestCase',
    'util.log.Traceable',
    'util.log.LogAppender'
  );

  /**
   * TestCase newinstance() functionality
   *
   * @purpose  Test newinstance()
   */
  class NewInstanceTest extends AbstractBootstrapTestCase {
  
    /**
     * Test creating an instance of the util.log.Traceable interface
     *
     */
    #[@test]
    public function newTraceable() {
      $appender= newinstance('util.log.Traceable', array(), '{
        function setTrace($cat) {
          // Intentionally empty
        }
      }');
      $this->assertSubclass($appender, 'util.log.Traceable');
    }


    /**
     * Test creating an instance of the util.log.LogAppender class
     *
     */
    #[@test]
    public function newLogAppender() {
      $appender= newinstance('util.log.LogAppender', array(), '{
        function append() { 
          // Intentionally empty
        }
      }');
      $this->assertSubclass($appender, 'util.log.LogAppender');
    }


    /**
     * Test arguments are passed constructor
     *
     */
    #[@test]
    public function argumentsArePassedToConstructor() {
      $appender= newinstance('util.log.LogAppender', array('[PREFIX]', 1), '{
        var $prefix, $severity;

        function __construct($prefix, $severity) {
          $this->prefix= $prefix;
          $this->severity= $severity;
        }

        function append() { 
          // Intentionally empty
        }
      }');
      $this->assertEquals('[PREFIX]', $appender->prefix);
      $this->assertEquals(1, $appender->severity);
    }

    /**
     * Helper method which will run the given newinstance() expression
     * in a sandbox and will assert a given fatal error is raised
     *
     * @see     xp://net.xp_framework.unittest.bootstrap.SandboxSourceRunner
     * @param   string message
     * @param   string expr
     * @throws  unittest.PrerequisitesNotMetError in case the sandbox runner cannot be setup
     */
    public function assertBailsWith($message, $expr) {
      
      // Run and verify exitcode 255
      $this->assertEquals(255, $this->sandbox->run('require("lang.base.php"); '.$expr));

      // Check for error message on STDERR and STDOUT
      $stderr= implode("\n", $this->sandbox->getStderr()).implode("\n", $this->sandbox->getStdout());
      if (1 !== preg_match('/'.preg_quote($message).'/i', $stderr)) {
        $this->fail('Error message incorrect', $stderr, $message);
      }
    }


    /**
     * Test failing to implement a method of the interface passed to
     * newinstance() will result in an error.
     *
     */
    #[@test]
    public function interfaceMethodNotImplemented() {
      $this->assertBailsWith(
        'contains 1 abstract method and must therefore be declared abstract or implement the remaining methods', 
        'uses("util.log.Traceable"); newinstance("util.log.Traceable", array(), "{}");'
      );
    }
    
    /**
     * Test passing a non-existant class to newinstance() will result in 
     * an error
     *
     */
    #[@test]
    public function nonExistantClass() {
      $this->assertBailsWith(
        'Class "@@NON-EXISTANT-CLASS@" does not exist', 
        'newinstance("@@NON-EXISTANT-CLASS@", array(), "{}");'
      );
    }

    /**
     * Test syntax errors in passed string will result in an error.
     *
     */
    #[@test]
    public function syntaxError() {
      $this->assertBailsWith(
        'Parse error', 
        'uses("util.log.LogAppender"); newinstance("util.log.LogAppender", array(), "{ @__SYNTAX ERROR__@ }");'
      );
    }
  }
?>
