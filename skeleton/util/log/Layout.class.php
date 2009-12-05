<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'util.log';

  uses('util.log.LoggingEvent');

  /**
   * Takes care of formatting log entries
   *
   */
  abstract class util·log·Layout extends Object {
    
    /**
     * Formats a logging event according to this layout
     *
     * @param   util.log.LoggingEvent event
     * @return  string
     */
    public abstract function format(LoggingEvent $event);
  }
?>
