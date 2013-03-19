<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.types.Number');

  /**
   * The Float class wraps a value of the type float
   *
   * @purpose  Wrapper
   */
  class Float extends Number {

    /**
     * ValueOf factory
     *
     * @param   string $value
     * @return  self
     */
    public static function valueOf($value) {
      return new self($value);
    }
  }
?>
