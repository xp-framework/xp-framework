<?php namespace net\xp_framework\unittest\remote;

/**
 * Handles the "mock" enum
 *
 * @see  xp://net.xp_framework.unittest.remote.SerializerTest
 */
class Enum extends \lang\Enum {
  public static
    $Value1= null,
    $Value2= null;

  public static function __static() {
    self::$Value1= new self(6100, 'Value1');
    self::$Value2= new self(6101, 'Value2');
  }
}