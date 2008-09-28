<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Log levels
   *
   * @see      xp://util.log.Logger
   * @purpose  Constants
   */
  class LogLevel extends Object {
    const 
      INFO  = 0x0001,
      WARN  = 0x0002,
      ERROR = 0x0004,
      DEBUG = 0x0008;
    
    const
      ALL   = 0x000F; // (INFO | WARN | ERROR | DEBUG)
  }
?>
