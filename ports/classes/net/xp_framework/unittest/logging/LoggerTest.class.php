<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.log.Logger'
  );

  /**
   * Tests Logger class
   *
   * @purpose  Unit Test
   */
  class LoggerTest extends TestCase {
    public
      $logger= NULL;
    
    /**
     * Setup method. Creates logger member for easier access to the
     * Logger instance
     *
     */
    public function setUp() {
      $this->logger= Logger::getInstance();
    }
    
    /**
     * Teardown method. Finalizes the logger.
     *
     */
    public function tearDown() {
      $this->logger->finalize();
    }
    
    /**
     * Ensure Logger is a singleton
     *
     */
    #[@test]
    public function loggerIsASingleton() {
      $this->assertTrue($this->logger === Logger::getInstance());
    }

    /**
     * Test a default category exists (but has no appenders)
     *
     */
    #[@test]
    public function defaultCategory() {
      with ($cat= $this->logger->getCategory()); {
        $this->assertClass($cat, 'util.log.LogCategory');
        $this->assertFalse($cat->hasAppenders());
      }
    }

    /**
     * Test Logger is configurable
     *
     */
    #[@test]
    public function isConfigurable() {
      $this->assertTrue(is('util.Configurable', $this->logger));
    }

    /**
     * Test configuring the logger
     *
     */
    #[@test]
    public function configureWithFlags() {
      $this->logger->configure(Properties::fromString(trim('
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql-errors_%Y-%m-%d.log"
appender.util.log.FileAppender.flags="LOGGER_FLAG_ERROR|LOGGER_FLAG_WARN"
      ')));
      
      with ($cat= $this->logger->getCategory('sql')); {
        $this->assertFalse($cat === $this->logger->getCategory());
        $this->assertClass($cat, 'util.log.LogCategory');
        $this->assertTrue($cat->hasAppenders());
      }
    }

    /**
     * Test configuring the logger
     *
     */
    #[@test]
    public function configureWithLevels() {
      $this->logger->configure(Properties::fromString(trim('
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql-errors_%Y-%m-%d.log"
appender.util.log.FileAppender.levels="ERROR|WARN"
      ')));
      
      with ($cat= $this->logger->getCategory('sql')); {
        $this->assertFalse($cat === $this->logger->getCategory());
        $this->assertClass($cat, 'util.log.LogCategory');
        $this->assertTrue($cat->hasAppenders());
      }
    }
  }
?>
