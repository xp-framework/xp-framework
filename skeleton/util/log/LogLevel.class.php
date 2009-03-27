<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Log levels
   *
   * @see      xp://util.log.Logger
   * @test     xp://net.xp_framework.unittest.logging.LogLevelTest
   * @purpose  Constants
   */
  abstract class LogLevel extends Object {
    const 
      INFO  = 0x0001,
      WARN  = 0x0002,
      ERROR = 0x0004,
      DEBUG = 0x0008;
    
    const
      NONE  = 0x0000,
      ALL   = 0x000F; // (INFO | WARN | ERROR | DEBUG)
    
    /**
     * Retrieve a loglevel by its name
     *
     * @param   string name
     * @return  int
     * @throws  lang.IllegalArgumentException
     */
    public static function named($name) {
      static $map= array(
        'INFO'  => self::INFO,
        'WARN'  => self::WARN,
        'ERROR' => self::ERROR,
        'DEBUG' => self::DEBUG,
        'ALL'   => self::ALL,
        'NONE'  => self::NONE,
      );
    
      $key= strtoupper($name);
      if (!isset($map[$key])) {
        throw new IllegalArgumentException('No such loglevel named "'.$name.'"');
      }
      return $map[$key];
    }
  }
?>
