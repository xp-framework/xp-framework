<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.LoggingEvent'
  );

  /**
   * TestCase
   *
   * @see      xp://util.log.LoggingEvent
   */
  class LoggingEventTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new LoggingEvent(
        new LogCategory('default', NULL, NULL, 0), 
        1258733284, 
        1, 
        LogLevel::INFO, 
        array('Hello')
      );
    }
  
    /**
     * Test getCategory() method
     *
     */
    #[@test]
    public function getCategory() {
      $this->assertEquals(new LogCategory('default', NULL, NULL, 0), $this->fixture->getCategory());
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
      $this->assertEquals(LogLevel::INFO, $this->fixture->getLevel());
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
?>
