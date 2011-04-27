<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.remote';

  uses('lang.Enum');

  /**
   * Handles the "mock" enum
   *
   * @see      xp://lang.Enum
   * @purpose  Enum
   */
  class net·xp_framework·unittest·remote·Enum extends Enum {
    public static
      $Value1= NULL,
      $Value2= NULL;
    
    public static function __static() {
      self::$Value1= new self(6100, 'Value1');
      self::$Value2= new self(6101, 'Value2');
    }
  } 
?>
