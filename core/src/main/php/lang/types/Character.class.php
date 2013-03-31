<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.Bytes');

  /**
   * Represents a character, which may consist of one or more bytes.
   *
   * Examples:
   * <code>
   *   $c= new Character(8364);               // The EUR symbol (U+20AC)
   *   $c= new Character(0x20AC);             // ...same, using hexadecimal
   *   $c= new Character('�', 'iso-8859-1');  // The German Umlaut A (capital)
   *
   *   $s= new String('�bercoder', 'iso-8859-1');
   *   $c= $s->charAt(0);                     // The German Umlaut U (capital)
   *   $c= $s[0];                             // ...same, via [] operator
   *
   *   $c= $s->charAt(1);                     // "b"
   *   $c= $s[1];                             // "b"
   * </code>
   *
   * @ext      iconv
   * @test     xp://net.xp_framework.unittest.core.types.CharacterTest
   * @purpose  Wrapper type
   */
  class Character extends Object {
    protected
      $buffer= '';

    /**
     * Constructor
     *
     * @param   var arg either a string or an int
     * @param   string charset default NULL
     */
    public function __construct($arg, $charset= NULL) {
      if (is_int($arg)) {
        $this->buffer= iconv('UCS-4BE', 'utf-8', pack('N', $arg));
        return;
      }        

      $charset= strtoupper($charset ? $charset : iconv_get_encoding('input_encoding'));

      // Convert the input to internal encoding
      $this->buffer= iconv($charset, 'utf-8', $arg);
      if (xp::errorAt(__FILE__, __LINE__ - 1)) {
        $message= key(xp::$errors[__FILE__][__LINE__ - 2]);
        xp::gc(__FILE__);
        throw new FormatException($message.($charset == 'utf-8'
          ? ' with charset '.$charset
          : $message.' while converting input from '.$charset.' to '.'utf-8'
        ));
      }

      if (1 != ($l= iconv_strlen($this->buffer, 'utf-8'))) {
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
      return iconv('utf-8', iconv_get_encoding('output_encoding').'//TRANSLIT', $this->buffer);
    }

    /**
     * Returns a string representation of this string. Uses the current
     * output encoding and transliteration.
     *
     * @return  string
     */
    public function __toString() {
      return iconv(STR_ENC, iconv_get_encoding('output_encoding').'//TRANSLIT', $this->buffer);
    }
   
    /**
     * Returns the bytes representing this character
     *
     * @param   string charset default NULL
     * @return  lang.types.Bytes
     */
    public function getBytes($charset= NULL) {
      $charset= strtoupper($charset ? $charset : iconv_get_encoding('input_encoding'));

      return new Bytes(STR_ENC === $charset 
        ? $this->buffer 
        : iconv(STR_ENC, $charset, $this->buffer)
      );
    }
  }
?>
