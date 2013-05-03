<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.Number');

  /**
   * The Byte class wraps a value of the type byte 
   * 
   * Range: -2^7 - (2^7)- 1
   */
  class Byte extends Number {

    /**
     * ValueOf factory
     *
     * @param   string $value
     * @return  self
     * @throws  lang.IllegalArgumentException
     */
    public static function valueOf($value) {
      if (is_int($value) || is_string($value) && strspn($value, '0123456789') === strlen($value)) {
        return new self($value);
      }
      throw new IllegalArgumentException('Not a byte: '.$value);
    }
  }
?>
