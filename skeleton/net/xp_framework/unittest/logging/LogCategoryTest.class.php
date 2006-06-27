<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'util.log.Logger',
    'util.log.LogAppender'
  );

  /**
   * Tests LogCategory class
   *
   * @purpose  Unit Test
   */
  class LogCategoryTest extends TestCase {
    var
      $logger= NULL,
      $cat   = NULL;
    
    /**
     * Static initializer. Declares MockAppender "inner" class.
     *
     * @model static
     */
    function __static() {
      $cl= &ClassLoader::getDefault();
      $cl->defineClass('LogCategoryTest$.MockAppender', 'class MockAppender extends LogAppender {
        var $messages= array();
        
        function append() { 
          $this->messages[]= func_get_args();
        }
      }');
    }
    
    /**
     * Setup method. Creates logger and cat member for easier access to
     * the Logger instance
     *
     * @access  public
     */
    function setUp() {
      $this->logger= &Logger::getInstance();
      $this->cat= &$this->logger->getCategory();
      $this->cat->format= '%3$s';
    }
    
    /**
     * Teardown method. Finalizes the logger.
     *
     * @access  public
     */
    function tearDown() {
      $this->logger->finalize();
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string method
     * @param   mixed[] args default ["Argument"]
     * @throws  util.profiling.AssertionFailedError
     */
    function assertLog($method, $args= array('Argument')) {
      $app= &$this->cat->addAppender(new MockAppender());
      call_user_func_array(array(&$this->cat, $method), $args);
      $this->assertEquals(array(array_merge((array)$method, $args)), $app->messages);
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   string method
     * @param   mixed[] args default ["Argument"]
     * @throws  util.profiling.AssertionFailedError
     */
    function assertLogf($method, $args= array('Argument')) {
      $app= &$this->cat->addAppender(new MockAppender());
      call_user_func_array(array(&$this->cat, $method), $args);
      $this->assertEquals(array(array_merge((array)substr($method, 0, -1), vsprintf(array_shift($args), $args))), $app->messages);
    }
    
    /**
     * Ensure the logger category initially has no appenders
     *
     * @access  public
     */
    #[@test]
    function initiallyNoAppenders() {
      $this->assertFalse($this->cat->hasAppenders());
    }

    /**
     * Tests adding an appender returns the added appender
     *
     * @access  public
     */
    #[@test]
    function addAppender() {
      $appender= &new MockAppender();
      $this->assertTrue($appender === $this->cat->addAppender($appender));
    }

    /**
     * Tests debug() method
     *
     * @access  public
     */
    #[@test]
    function debug() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests debugf() method
     *
     * @access  public
     */
    #[@test]
    function debugf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests info() method
     *
     * @access  public
     */
    #[@test]
    function info() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests infof() method
     *
     * @access  public
     */
    #[@test]
    function infof() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests warn() method
     *
     * @access  public
     */
    #[@test]
    function warn() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests warnf() method
     *
     * @access  public
     */
    #[@test]
    function warnf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests error() method
     *
     * @access  public
     */
    #[@test]
    function error() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests errorf() method
     *
     * @access  public
     */
    #[@test]
    function errorf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }
  }
?>
