<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.Appender',
    'util.log.LoggingEvent',
    'util.log.LogCategory'
  );

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
?>
