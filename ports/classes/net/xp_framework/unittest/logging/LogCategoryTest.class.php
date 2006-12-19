<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.log.Logger',
    'util.log.LogAppender'
  );

  /**
   * Tests LogCategory class
   *
   * @purpose  Unit Test
   */
  class LogCategoryTest extends TestCase {
    public
      $logger= NULL,
      $cat   = NULL;
    
    /**
     * Setup method. Creates logger and cat member for easier access to
     * the Logger instance
     *
     * @access  public
     */
    public function setUp() {
      $this->logger= &Logger::getInstance();
      $this->cat= &$this->logger->getCategory();
      $this->cat->format= '%3$s';
    }
    
    /**
     * Teardown method. Finalizes the logger.
     *
     * @access  public
     */
    public function tearDown() {
      $this->logger->finalize();
    }
    
    /**
     * Create a mock appender which simply stores all messages passed to 
     * its append() method.
     *
     * @access  protected
     * @return  &util.log.LogAppender
     */
    public function &mockAppender() {
      return newinstance('util.log.LogAppender', array(), '{
        var $messages= array();
        
        function append() { 
          $this->messages[]= func_get_args();
        }
      }');
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string method
     * @param   mixed[] args default ["Argument"]
     * @throws  unittest.AssertionFailedError
     */
    public function assertLog($method, $args= array('Argument')) {
      $app= &$this->cat->addAppender($this->mockAppender());
      call_user_func_array(array(&$this->cat, $method), $args);
      $this->assertEquals(array(array_merge((array)$method, $args)), $app->messages);
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   string method
     * @param   mixed[] args default ["Argument"]
     * @throws  unittest.AssertionFailedError
     */
    public function assertLogf($method, $args= array('Argument')) {
      $app= &$this->cat->addAppender($this->mockAppender());
      call_user_func_array(array(&$this->cat, $method), $args);
      $this->assertEquals(array(array_merge((array)substr($method, 0, -1), (array)vsprintf(array_shift($args), $args))), $app->messages);
    }
    
    /**
     * Ensure the logger category initially has no appenders
     *
     * @access  public
     */
    #[@test]
    public function initiallyNoAppenders() {
      $this->assertFalse($this->cat->hasAppenders());
    }

    /**
     * Tests adding an appender returns the added appender
     *
     * @access  public
     */
    #[@test]
    public function addAppender() {
      $appender= &$this->mockAppender();
      $this->assertTrue($appender === $this->cat->addAppender($appender));
    }

    /**
     * Tests debug() method
     *
     * @access  public
     */
    #[@test]
    public function debug() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests debugf() method
     *
     * @access  public
     */
    #[@test]
    public function debugf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests info() method
     *
     * @access  public
     */
    #[@test]
    public function info() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests infof() method
     *
     * @access  public
     */
    #[@test]
    public function infof() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests warn() method
     *
     * @access  public
     */
    #[@test]
    public function warn() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests warnf() method
     *
     * @access  public
     */
    #[@test]
    public function warnf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests error() method
     *
     * @access  public
     */
    #[@test]
    public function error() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests errorf() method
     *
     * @access  public
     */
    #[@test]
    public function errorf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }
  }
?>
