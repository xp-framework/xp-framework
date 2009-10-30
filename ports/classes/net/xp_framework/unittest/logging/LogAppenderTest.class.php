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
   * TestCase
   *
   * @see      xp://util.log.LogAppender
   * @purpose  Unittest
   */
  class LogAppenderTest extends TestCase {
    
    /**
     * Check simple configuration for appender
     *
     */
    #[@test]
    public function configure() {
      Logger::getInstance()->configure(Properties::fromString('
[default]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.param.filename="some-file.log"
      '));
      
      $cat= Logger::getInstance()->getCategory();
      $app= $cat->_appenders[key($cat->_appenders)][0];
      
      $this->assertClass($app, 'util.log.FileAppender');
      $this->assertEquals('some-file.log', $app->filename);
    }

    /**
     * Invalid configuration should throw an exception
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function invalidConfiguration() {
      Logger::getInstance()->configure(Properties::fromString('
[default]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.param.illegal="some-file.log"
      '));
    }

    /**
     * Verify that filename is passed w/ date tokens in it, so
     * the FileAppender can evaluate the file to log to all the
     * time (even if day border was passed).
     *
     */
    #[@test]
    public function filenameContainsDateTokens() {
      Logger::getInstance()->configure(Properties::fromString('
[default]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.param.filename="daily-%Y-%m-%d.log"
      '));

      $cat= Logger::getInstance()->getCategory();
      $app= $cat->_appenders[key($cat->_appenders)][0];

      $this->assertEquals('daily-%Y-%m-%d.log', $app->filename);
    }
  }
?>
