<?php namespace net\xp_framework\unittest\logging;

use unittest\TestCase;
use util\log\Appender;
use util\log\LoggingEvent;
use util\log\LogCategory;


/**
 * TestCase for AppenderTest
 */
abstract class AppenderTest extends TestCase {

  /**
   * Creates new logging event
   *
   * @param   int level see util.log.LogLevel
   * @param   string message
   * @return  util.log.LoggingEvent
   */
  protected function newEvent($level, $message) {
    return new LoggingEvent(new LogCategory('test'), 0, 0, $level, array($message));
  }
}
