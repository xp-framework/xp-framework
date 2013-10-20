<?php namespace net\xp_framework\unittest\logging;

use unittest\TestCase;
use util\log\LoggingEvent;


/**
 * TestCase
 *
 * @see      xp://util.log.LoggingEvent
 */
class LoggingEventTest extends TestCase {
  protected $fixture= null;

  /**
   * Creates fixture
   *
   */
  public function setUp() {
    $this->fixture= new LoggingEvent(
      new \util\log\LogCategory('default', null, null, 0), 
      1258733284, 
      1, 
      \util\log\LogLevel::INFO, 
      array('Hello')
    );
  }

  /**
   * Test getCategory() method
   *
   */
  #[@test]
  public function getCategory() {
    $this->assertEquals(new \util\log\LogCategory('default', null, null, 0), $this->fixture->getCategory());
  }
 
  /**
   * Test getTimestamp() method
   *
   */
  #[@test]
  public function getTimestamp() {
    $this->assertEquals(1258733284, $this->fixture->getTimestamp());
  }

  /**
   * Test getProcessId() method
   *
   */
  #[@test]
  public function getProcessId() {
    $this->assertEquals(1, $this->fixture->getProcessId());
  }

  /**
   * Test getLevel() method
   *
   */
  #[@test]
  public function getLevel() {
    $this->assertEquals(\util\log\LogLevel::INFO, $this->fixture->getLevel());
  }

  /**
   * Test getArguments() method
   *
   */
  #[@test]
  public function getArguments() {
    $this->assertEquals(array('Hello'), $this->fixture->getArguments());
  }
}
