<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('STR_ENC', 'UTF-8');

  uses('lang.types.Character', 'lang.types.Bytes');

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
      } else if (is_string($arg) || $arg instanceof Bytes) {
        $charset= strtoupper($charset ? $charset : iconv_get_encoding('input_encoding'));

        // Convert the input to internal encoding
        $buffer= iconv($charset, STR_ENC, $arg);
        if (xp::errorAt(__FILE__, __LINE__ - 1)) {
          $message= key(xp::$registry['errors'][__FILE__][__LINE__ - 2]);
          xp::gc();
          throw new FormatException($message.($charset == STR_ENC  
            ? ' with charset '.$charset
            : $message.' while converting input from '.$charset.' to '.STR_ENC
          ));
        }
        return $buffer;
      } else {
        return (string)$arg;
      }
    }

    /**
     * Constructor
     *
     * @param   string initial default ''
     * @param   string charset default NULL
     */
    public function __construct($initial= '', $charset= NULL) {
      $this->buffer= $this->asIntern($initial, $charset);
      $this->length= iconv_strlen($this->buffer, STR_ENC);
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
     * @param   mixed value
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
      if (1 != iconv_strlen($char, STR_ENC)) {
        throw new IllegalArgumentException('Set only allows to set one character!');
      }
      
      $this->buffer= (
        iconv_substr($this->buffer, 0, $offset, STR_ENC).
        $char.
        iconv_substr($this->buffer, $offset+ 1, $this->length, STR_ENC)
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
        iconv_substr($this->buffer, 0, $offset, STR_ENC).
        iconv_substr($this->buffer, $offset+ 1, $this->length, STR_ENC)
      );
      $this->length= iconv_strlen($this->buffer, STR_ENC);
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
      return new Character(iconv_substr($this->buffer, $offset, 1, STR_ENC), STR_ENC);
    }

    /**
     * Returns the index within this string of the first occurrence of 
     * the specified substring.
     *
     * @param   mixed arg either a string or a String
     * @param   int start default 0
     * @return  bool
     */
    public function indexOf($arg, $start= 0) {
      $r= iconv_strpos($this->buffer, $this->asIntern($arg), $start, STR_ENC);
      return FALSE === $r ? -1 : $r;
    }

    /**
     * Returns the index within this string of the last occurrence of 
     * the specified substring.
     *
     * @param   mixed arg either a string or a String
     * @return  bool
     */
    public function lastIndexOf($arg) {
      $r= iconv_strrpos($this->buffer, $this->asIntern($arg), STR_ENC);
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
      return new self(iconv_substr($this->buffer, $start, $length, STR_ENC), STR_ENC);
    }

    /**
     * Returns whether a given substring is contained in this string
     *
     * @param   mixed arg
     * @return  bool
     */
    public function contains($arg) {
      return -1 != $this->indexOf($arg);
    }

    /**
     * Returns whether a given substring is contained in this string
     *
     * @param   mixed old
     * @param   mixed new default ''
     * @return  lang.types.String this string
     */
    public function replace($old, $new= '') {
      $this->buffer= str_replace($this->asIntern($old), $this->asIntern($new), $this->buffer);
      $this->length= iconv_strlen($this->buffer, STR_ENC);
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
     * @param   mixed arg
     * @return  lang.types.String this string
     */
    public function concat($arg) {
      $this->buffer.= $this->asIntern($arg);
      $this->length= iconv_strlen($this->buffer, STR_ENC);
      return $this;
    }

    /**
     * Returns whether this string starts with a given argument.
     *
     * @param   mixed arg either a string or a String
     * @return  bool
     */
    public function startsWith($arg) {
      return 0 == $this->indexOf($arg);
    }

    /**
     * Returns whether this string starts with a given argument.
     *
     * @param   mixed arg either a string or a String
     * @return  bool
     */
    public function endsWith($arg) {
      $bytes= $this->asIntern($arg);

      return (
        iconv_strlen($this->buffer, STR_ENC) - iconv_strlen($bytes, STR_ENC) === 
        iconv_strrpos($this->buffer, $bytes, STR_ENC)
      );
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
     * @param   string charset default 'UTF-8'
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
