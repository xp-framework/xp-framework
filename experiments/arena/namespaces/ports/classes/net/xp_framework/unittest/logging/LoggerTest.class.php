<?php
/* This class is part of the XP framework
 *
 * $Id: LoggerTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::logging;
 
  ::uses(
    'unittest.TestCase',
    'util.log.Logger'
  );

  /**
   * Tests Logger class
   *
   * @purpose  Unit Test
   */
  class LoggerTest extends unittest::TestCase {
    public
      $logger= NULL;
    
    /**
     * Setup method. Creates logger member for easier access to the
     * Logger instance
     *
     */
    public function setUp() {
      $this->logger= util::log::Logger::getInstance();
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
      $this->assertTrue($this->logger === util::log::Logger::getInstance());
    }

    /**
     * Test a default category exists (but has no appenders)
     *
     */
    #[@test]
    public function defaultCategory() {
      ::with ($cat= $this->logger->getCategory()); {
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
      $this->assertTrue(::is('Configurable', $this->logger));
    }

    /**
     * Test configuring the logger
     *
     */
    #[@test]
    public function configure() {
      $this->logger->configure(util::Properties::fromString(<<<__
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql-errors_%Y-%m-%d.log"
appender.util.log.FileAppender.flags="LOGGER_FLAG_ERROR|LOGGER_FLAG_WARN"
__
      ));
      
      ::with ($cat= $this->logger->getCategory('sql')); {
        $this->assertFalse($cat === $this->logger->getCategory());
        $this->assertClass($cat, 'util.log.LogCategory');
        $this->assertTrue($cat->hasAppenders());
      }
    }
  }
?>
