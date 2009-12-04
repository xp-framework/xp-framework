<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Layout');

  /**
   * Default layout
   *
   */
  class DefaultLayout extends util·log·Layout {
  
    /**
     * Creates a string representation of the given argument. For any 
     * string given, the result is the string itself, for any other type,
     * the result is the xp::stringOf() output.
     *
     * @param   var arg
     * @return  string
     */
    protected function stringOf($arg) {
      return is_string($arg) ? $arg : xp::stringOf($arg);
    }

    /**
     * Formats a logging event according to this layout
     *
     * @param   util.log.LoggingEvent event
     * @return  string
     */
    public function format(LoggingEvent $event) {
      return sprintf(
        "[%s %5d %5s] %s\n", 
        date('H:i:s', $event->getTimestamp()),
        $event->getProcessId(),
        strtolower(LogLevel::nameOf($event->getLevel())),
        implode(' ', array_map(array($this, 'stringOf'), $event->getArguments()))
      );
    }
  }
?>
