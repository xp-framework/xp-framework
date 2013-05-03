<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.types.Number');

  /**
   * The Integer class wraps a value of the type int 
   * 
   * Range: -2^31 - (2^31)- 1
   */
  class Integer extends Number {

    /**
     * ValueOf factory
     *
     * @param   string $value
     * @return  self
     * @throws  lang.IllegalArgumentException
     */
    public static function valueOf($value) {
      if (!is_numeric($value)) {
        throw new IllegalArgumentException('Not a number: '.$value);
      }
      return new self($value);
    }
  }
?>
