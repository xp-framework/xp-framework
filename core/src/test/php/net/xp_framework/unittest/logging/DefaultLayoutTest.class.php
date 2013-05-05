<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.layout.DefaultLayout'
  );

  /**
   * TestCase for DefaultLayout
   *
   * @see   xp://util.log.layout.DefaultLayout
   */
  class DefaultLayoutTest extends TestCase {

    /**
     * Sets up test case and backups Console::$err stream.
     *
     */
    public function setUp() {
      $this->fixture= new DefaultLayout();
    }

    /**
     * Creates new logging event
     *
     * @param   int level see util.log.LogLevel
     * @param   string message
     * @return  util.log.LoggingEvent
     */
    public function newEvent($level, $args) {
      return new LoggingEvent(new LogCategory('test'), 0, 0, $level, $args);
    }


    /**
     * Test format() method
     */
    #[@test]
    public function debug() {
      $this->assertEquals(
        "[01:00:00     0 debug] Test\n",
        $this->fixture->format($this->newEvent(LogLevel::DEBUG, array('Test')))
      );
    }

    /**
     * Test format() method
     */
    #[@test]
    public function info() {
      $this->assertEquals(
        "[01:00:00     0  info] Test\n",
        $this->fixture->format($this->newEvent(LogLevel::INFO, array('Test')))
      );
    }

    /**
     * Test format() method
     */
    #[@test]
    public function warn() {
      $this->assertEquals(
        "[01:00:00     0  warn] Test\n",
        $this->fixture->format($this->newEvent(LogLevel::WARN, array('Test')))
      );
    }

    /**
     * Test format() method
     */
    #[@test]
    public function error() {
      $this->assertEquals(
        "[01:00:00     0 error] Test\n",
        $this->fixture->format($this->newEvent(LogLevel::ERROR, array('Test')))
      );
    }
  }
?>
