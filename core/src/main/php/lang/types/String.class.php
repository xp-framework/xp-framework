<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('STR_ENC', 'utf-8');

  uses('lang.types.Character', 'lang.types.Bytes');

  if (extension_loaded('mbstring')) {
    mb_internal_encoding(STR_ENC);
    class __str {
      static function len($buf) { return mb_strlen($buf); }
      static function substr($buf, $start, $length) { return mb_substr($buf, $start, $length); }
      static function pos($buf, $needle, $start) { return mb_strpos($buf, $needle, $start); }
      static function rpos($buf, $needle) { return mb_strrpos($buf, $needle); }
    }
  } else {
    iconv_set_encoding('internal_encoding', STR_ENC);
    class __str {
      static function len($buf) { return iconv_strlen($buf); }
      static function substr($buf, $start, $length) { return iconv_substr($buf, $start, $length); }
      static function pos($buf, $needle, $start) { return iconv_strpos($buf, $needle, $start); }
      static function rpos($buf, $needle) { return iconv_strrpos($buf, $needle); }
    }
  }

  /**
   * Represents a string
   *
   * @ext      iconv
   * @test     xp://net.xp_framework.unittest.core.types.StringTest
   * @purpose  Wrapper type
   */
  class String extends Object implements ArrayAccess {
    protected 
      $buffer= '',
      $length= 0;

    public static $EMPTY = NULL;

    static function __static() {
      self::$EMPTY= new self('', STR_ENC);
    }

    /**
     * Convert a string to internal encoding
     *
     * @param   string string
     * @param   string charset default NULL
     * @return  string
     * @throws  lang.FormatException in case a conversion error occurs
     */
    protected function asIntern($arg, $charset= NULL) {
      if ($arg instanceof self) {
        return $arg->buffer;
      } else if ($arg instanceof Character) {
        return $arg->getBytes(STR_ENC)->buffer;
      } else {
        $charset= strtoupper($charset ? $charset : iconv_get_encoding('input_encoding'));

        // Convert the input to internal encoding
        $buffer= iconv($charset, STR_ENC, $arg);
        if (xp::errorAt(__FILE__, __LINE__ - 1)) {
          $message= key(xp::$errors[__FILE__][__LINE__ - 2]);
          xp::gc(__FILE__);
          throw new FormatException($message.($charset == STR_ENC  
            ? ' with charset '.$charset
            : $message.' while converting input from '.$charset.' to '.STR_ENC
          ));
        }
        return $buffer;
      }
    }

    /**
     * Constructor
     *
     * @param   string initial default ''
     * @param   string charset default NULL
     */
    public function __construct($initial= '', $charset= NULL) {
      if (NULL === $initial) return;
      $this->buffer= $this->asIntern($initial, $charset);
      $this->length= __str::len($this->buffer);
    }

    /**
     * = list[] overloading
     *
     * @param   int offset
     * @return  lang.types.Character
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function offsetGet($offset) {
      return $this->charAt($offset);
    }

    /**
     * list[]= overloading
     *
     * @param   int offset
     * @param   var value
     * @throws  lang.IllegalArgumentException if key is neither numeric (set) nor NULL (add)
     */
    public function offsetSet($offset, $value) {
      if (!is_int($offset)) {
        throw new IllegalArgumentException('Incorrect type '.gettype($offset).' for index');
      }
      
      if ($offset >= $this->length || $offset < 0) {
        raise('lang.IndexOutOfBoundsException', 'Offset '.$offset.' out of bounds');
      }
      
      $char= $this->asIntern($value);
      if (1 != __str::len($char)) {
        throw new IllegalArgumentException('Set only allows to set one character!');
      }
      
      $this->buffer= (
        __str::substr($this->buffer, 0, $offset).
        $char.
        __str::substr($this->buffer, $offset+ 1, $this->length)
      );
    }

    /**
     * isset() overloading
     *
     * @param   int offset
     * @return  bool
     */
    public function offsetExists($offset) {
      return ($offset >= 0 && $offset < $this->length);
    }

    /**
     * unset() overloading
     *
     * @param   int offset
     */
    public function offsetUnset($offset) {
      if ($offset >= $this->length || $offset < 0) {
        raise('lang.IndexOutOfBoundsException', 'Offset '.$offset.' out of bounds');
      }
      $this->buffer= (
        __str::substr($this->buffer, 0, $offset).
        __str::substr($this->buffer, $offset+ 1, $this->length)
      );
      $this->length= __str::len($this->buffer);
    }

    /**
     * Returns the string's length (the number of characters in this
     * string, not the number of bytes)
     *
     * @return  string
     */
    public function length() {
      return $this->length;
    }

    /**
     * Returns the character at the given position
     *
     * @param   int offset
     * @return  lang.types.Character
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function charAt($offset) {
      if ($offset >= $this->length || $offset < 0) {
        raise('lang.IndexOutOfBoundsException', 'Offset '.$offset.' out of bounds');
      }
      return new Character(__str::substr($this->buffer, $offset, 1), STR_ENC);
    }

    /**
     * Returns the index within this string of the first occurrence of 
     * the specified substring.
     *
     * @param   var arg either a string or a String
     * @param   int start default 0
     * @return  bool
     */
    public function indexOf($arg, $start= 0) {
      if ('' === ($needle= $this->asIntern($arg))) return -1;
      $r= __str::pos($this->buffer, $needle, $start);
      return FALSE === $r ? -1 : $r;
    }

    /**
     * Returns the index within this string of the last occurrence of 
     * the specified substring.
     *
     * @param   var arg either a string or a String
     * @return  bool
     */
    public function lastIndexOf($arg) {
      if ('' === ($needle= $this->asIntern($arg))) return -1;
      $r= __str::rpos($this->buffer, $needle);
      return FALSE === $r ? -1 : $r;
    }

    /**
     * Returns a new string that is a substring of this string.
     *
     * @param   int start
     * @param   int length default 0
     * @return  lang.types.String
     */
    public function substring($start, $length= 0) {
      if (0 === $length) $length= $this->length;
      $self= new self(NULL);
      $self->buffer= __str::substr($this->buffer, $start, $length);
      $self->length= __str::len($self->buffer);
      return $self;
    }

    /**
     * Returns whether a given substring is contained in this string
     *
     * @param   var arg
     * @return  bool
     */
    public function contains($arg) {
      if ('' === ($needle= $this->asIntern($arg))) return FALSE;
      return FALSE !== __str::pos($this->buffer, $needle, 0);
    }

    /**
     * Returns whether a given substring is contained in this string
     *
     * @param   var old
     * @param   var new default ''
     * @return  lang.types.String this string
     */
    public function replace($old, $new= '') {
      $this->buffer= str_replace($this->asIntern($old), $this->asIntern($new), $this->buffer);
      $this->length= __str::len($this->buffer);
      return $this;
    }

    /**
     * Concatenates the given argument to the end of this string and returns 
     * this String so it can be used in chained calls:
     * 
     * <code>
     *   $s= new String('Hello');
     *   $s->concat(' ')->concat('World');
     * </code>
     *
     * @param   var arg
     * @return  lang.types.String this string
     */
    public function concat($arg) {
      $this->buffer.= $this->asIntern($arg);
      $this->length= __str::len($this->buffer);
      return $this;
    }

    /**
     * Returns whether this string starts with a given argument.
     *
     * @param   var arg either a string or a String
     * @return  bool
     */
    public function startsWith($arg) {
      $bytes= $this->asIntern($arg);
      return 0 === strncmp($this->buffer, $bytes, strlen($bytes));
    }

    /**
     * Returns whether this string ends with a given argument.
     *
     * @param   var arg either a string or a String
     * @return  bool
     */
    public function endsWith($arg) {
      $bytes= $this->asIntern($arg);
      $l= strlen($bytes);
      return 0 === substr_compare($this->buffer, $bytes, -$l, $l);
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
      return md5($this->buffer);
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
     * Returns a string representation of this string. Uses the current
     * output encoding and transliteration.
     *
     * @return  string
     */
    public function __toString() {
      return iconv(STR_ENC, iconv_get_encoding('output_encoding').'//TRANSLIT', $this->buffer);
    }
   
    /**
     * Returns the bytes representing this string
     *
     * @param   string charset default 'utf-8'
     * @return  lang.types.Bytes
     */
    public function getBytes($charset= NULL) {
      $charset= strtoupper($charset ? $charset : iconv_get_encoding('input_encoding'));
      if (STR_ENC === $charset) {
        return new Bytes($this->buffer);
      }
      $bytes= iconv(STR_ENC, $charset, $this->buffer);
      if (xp::errorAt(__FILE__, __LINE__ - 1)) {
        $message= key(xp::$errors[__FILE__][__LINE__ - 2]);
        xp::gc(__FILE__);
        throw new FormatException($message.' while converting input from '.STR_ENC.' to '.$charset);
      }
      return new Bytes($bytes);
    }
  }
?>
