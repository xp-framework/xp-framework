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
    protected $logger= NULL;
    
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
      $this->assertSubclass($this->logger, 'util.Configurable');
    }

    /**
     * Test configuring the logger
     *
     */
    #[@test]
    public function configureMultipleCategories() {
      $this->logger->configure(Properties::fromString(trim('
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql.log"

[remote]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/remote.log"
      ')));
      
      with ($sql= $this->logger->getCategory('sql')); {
        $appenders= $sql->getAppenders();
        $this->assertClass($appenders[0], 'util.log.FileAppender');
        $this->assertEquals('/var/log/xp/sql.log', $appenders[0]->filename);
      }
      
      with ($sql= $this->logger->getCategory('remote')); {
        $appenders= $sql->getAppenders();
        $this->assertClass($appenders[0], 'util.log.FileAppender');
        $this->assertEquals('/var/log/xp/remote.log', $appenders[0]->filename);
      }
    }

    /**
     * Test configuring the logger
     *
     */
    #[@test]
    public function configureMultipleAppenders() {
      $this->logger->configure(Properties::fromString(trim('
[sql]
appenders="util.log.FileAppender|util.log.SmtpAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql.log"
appender.util.log.SmtpAppender.params="email"
appender.util.log.SmtpAppender.param.email="xp@example.com"
      ')));
      
      with ($sql= $this->logger->getCategory('sql')); {
        $appenders= $sql->getAppenders();
        $this->assertClass($appenders[0], 'util.log.FileAppender');
        $this->assertEquals('/var/log/xp/sql.log', $appenders[0]->filename);
        $this->assertClass($appenders[1], 'util.log.SmtpAppender');
        $this->assertEquals('xp@example.com', $appenders[1]->email);
      }
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
        with ($appenders= $cat->getAppenders(LogLevel::ERROR | LogLevel::WARN)); {
          $this->assertClass($appenders[0], 'util.log.FileAppender');
        }
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
        with ($appenders= $cat->getAppenders(LogLevel::ERROR | LogLevel::WARN)); {
          $this->assertClass($appenders[0], 'util.log.FileAppender');
        }
      }
    }
  }
?>
