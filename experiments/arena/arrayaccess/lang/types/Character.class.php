<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a character
   *
   * @ext      iconv
   * @purpose  Wrapper type
   */
  class Character extends Object {
    protected
      $buffer= '';

    /**
     * Constructor
     *
     * @param   mixed arg either a string or an int
     * @param   string charset default NULL
     */
    public function __construct($arg, $charset= NULL) {
      if (is_int($arg)) {
        $this->buffer= iconv('UCS-4BE', STR_ENC, pack('N', $arg));
        return;
      }        

      if (!$charset) $charset= iconv_get_encoding('input_encoding');

      // Convert the input to internal encoding
      $this->buffer= iconv($charset, STR_ENC, $arg);
      if (xp::errorAt(__FILE__, __LINE__ - 1)) {
        $message= key(xp::$registry['errors'][__FILE__][__LINE__ - 2]);
        xp::gc();
        throw new FormatException($message.($charset == STR_ENC  
          ? ' with charset '.$charset
          : $message.' while converting input from '.$charset.' to '.STR_ENC
        ));
      }

      if (1 != ($l= iconv_strlen($this->buffer, STR_ENC))) {
        throw new IllegalArgumentException('Given argument is too long ('.$l.')');
      }
    }

    /**
     * Returns whether a given object is equal to this object
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->buffer === $cmp->buffer;
    }

    /**
     * Returns a hashcode for this string object
     *
     * @return  string
     */
    public function hashCode() {
      return $this->buffer;
    }

    /**
     * Returns a string representation of this string. Uses the current
     * output encoding and transliteration.
     *
     * @return  string
     */
    public function toString() {
      return iconv(STR_ENC, iconv_get_encoding('output_encoding').'//TRANSLIT', $this->buffer);
    }

    /**
     * Returns the bytes in internal encoding (UTF-8)
     *
     * @return  string
     */
    public function getBytes() {
      return $this->buffer;
    }
  }
?>
