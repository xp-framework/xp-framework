<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.LogLevel'
  );

  /**
   * TestCase
   *
   * @see      xp://util.log.LogLevel
   * @purpose  Unittest
   */
  class LogLevelTest extends TestCase {
  
    /**
     * Test named() method
     *
     */
    #[@test]
    public function namedInfo() {
      $this->assertEquals(LogLevel::INFO, LogLevel::named('INFO'));
    }

    /**
     * Test named() method
     *
     */
    #[@test]
    public function namedWarn() {
      $this->assertEquals(LogLevel::WARN, LogLevel::named('WARN'));
    }

    /**
     * Test named() method
     *
     */
    #[@test]
    public function namedError() {
      $this->assertEquals(LogLevel::ERROR, LogLevel::named('ERROR'));
    }

    /**
     * Test named() method
     *
     */
    #[@test]
    public function namedDebug() {
      $this->assertEquals(LogLevel::DEBUG, LogLevel::named('DEBUG'));
    }

    /**
     * Test named() method
     *
     */
    #[@test]
    public function namedAll() {
      $this->assertEquals(LogLevel::ALL, LogLevel::named('ALL'));
    }

    /**
     * Test named() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unknown() {
      LogLevel::named('@UNKNOWN@');
    }
  }
?>
