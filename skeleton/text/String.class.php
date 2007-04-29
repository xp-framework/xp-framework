<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.IndexOutOfBoundsException');

  define('CR',      "\r");
  define('LF',      "\n");
  define('CRLF',    "\r\n");

  /**
   * Represents a string. 
   *
   * This class is useful in two situations:
   * <ul>
   *  <li>You have very large strings. The overhead is thus not 
   *      noticeable and as objects are passed by reference instead
   *      of by value, it will actually save memory!
   *  </li>
   *  <li>You want an object-oriented API</li>
   * </ul>
   *
   * @deprecated Use lang.types.String instead!
   * @see      php://strings
   * @purpose  Type wrapper
   */
  class String extends Object {
    public 
      $buffer   = '';

    /**
     * Constructor
     *
     * @param   string initial default ''
     */
    public function __construct($initial= '') {
      $this->buffer= $initial;
    }
    
    /**
     * Retrieve string's length
     *
     * @return  int
     */
    public function length() {
      return strlen($this->buffer);
    }

    /**
     * Set Buffer
     *
     * @param   string buffer
     */
    public function setBuffer($buffer) {
      $this->buffer= $buffer;
    }

    /**
     * Get Buffer
     *
     * @return  string
     */
    public function getBuffer() {
      return $this->buffer;
    }
    
    /**
     * Returns the character at the specified index. Index counting starts
     * at 0 and ends at length() - 1. Use -1 as value for the pos argument
     * to retrieve the last character in this string.
     *
     * @param   int pos
     * @return  string character
     * @throws  lang.IndexOutOfBoundsException
     */
    public function charAt($pos) {
      if (-1 == $pos) {
        $pos= strlen($this->buffer)- 1;
      } else if ($pos < 0 || $pos >= strlen($this->buffer)) {
        throw(new IndexOutOfBoundsException($pos.' is not a valid string offset'));
      }

      return $this->buffer{$pos};
    }
    
    /**
     * Compares two strings lexicographically.
     *
     * @param   &text.String string
     * @param   bool cs default TRUE whether to compare case-sensitively
     * @return  int
     * @see     php://strcmp for case-sensitive comparison
     * @see     php://strcasecmp for case-insensitive comparison
     */
    public function compareTo($string, $cs= TRUE) {
      return ($cs 
        ? strcmp($string->buffer, $this->buffer) 
        : strcasecmp($string->buffer, $this->buffer)
      );
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->buffer);
    }

    /**
     * Returns true if this string equals another string
     *
     * @param   &lang.Object value
     * @return  bool
     */
    public function equals($cmp) {
      return (
        is('String', $cmp) && 
        ($this->buffer === $cmp->buffer)
      );
    }

    /**
     * Returns true if the specified string matches this string.
     *
     * @param   string str
     * @return  bool
     */
    public function isEqualTo($str, $cs= TRUE) {
      return 0 == ($cs 
        ? strcmp($str, $this->buffer) 
        : strcasecmp($str, $this->buffer)
      );
    }
     
    /**
     * Compares two strings lexicographically using a "natural order" 
     * algorithm
     *
     * @param   &text.String string
     * @param   bool cs default TRUE whether to compare case-sensitively
     * @return  int
     * @see     php://strnatcmp for case-sensitive comparison
     * @see     php://strnatcasecmp for case-insensitive comparison
     */
    public function compareToNat($string, $cs= TRUE) {
      return ($cs 
        ? strnatcmp($string->buffer, $this->buffer) 
        : strnatcasecmp($string->buffer, $this->buffer)
      );
    }
   
    /**
     * Tests if this string starts with the specified prefix beginning 
     * a specified index.
     *
     * @param   string prefix
     * @param   int offset default 0 where to begin looking in the string
     * @return  bool
     */
    public function startsWith($prefix, $offset= 0) {
      return substr($this->buffer, $offset, strlen($prefix)) == $prefix;
    }
    
    /**
     * Tests if this string ends with the specified suffix.
     *
     * @param   string suffix
     * @return  bool
     */
    public function endsWith($suffix) {
      return substr($this->buffer, -1 * strlen($suffix)) == $suffix;
    }
    
    /**
     * Returns the index within this string of the first occurrence of the 
     * specified substring
     *
     * @param   string substr
     * @param   int offset default 0 the index to start the search from
     * @return  int the index of the first occurrence of the substring or FALSE
     * @see     php://strpos
     */
    public function indexOf($substr, $offset= 0) {
      return strpos($this->buffer, $substr, $offset);
    }
    
    /**
     * Returns the index within this string of the last occurrence of the 
     * specified substring
     *
     * @param   string substr
     * @return  int the index of the first occurrence of the substring or FALSE
     * @see     php://strrpos
     */
    public function lastIndexOf($substr) {
      return strrpos($this->buffer, $substr);
    }
    
    /**
     * Returns whether the specified substring is contained in this string
     *
     * @param   string substr
     * @param   bool cs default TRUE whether to check case-sensitively
     * @return  bool
     */
    public function contains($substr, $cs= TRUE) {
      return ($cs 
        ? FALSE !== strpos($this->buffer, $substr)
        : FALSE !== strpos(strtolower($this->buffer), strtolower($substr))
      );
    }
    
    /**
     * Find first occurrence of a string.  Returns part of haystack string 
     * from the first occurrence of needle to the end of haystack. 
     *
     * Example:
     * <code>
     *   $s= &new String('xp@php3.de');
     *   if ($portion= $s->substrAfter('@')) {
     *     echo $portion;   // php3.de
     *   }
     * </code>
     *
     * @param   string substr
     * @param   bool cs default TRUE whether to check case-sensitively
     * @return  string or FALSE if substr is not found
     * @see     php://strstr
     */
    public function substrAfter($substr, $cs= TRUE) {
      return ($cs 
        ? strstr($this->buffer, $substr)
        : stristr($this->buffer, $substr)
      );
    }

    /**
     * Find first occurrence of a string.  Returns part of haystack string 
     * from the first occurrence of needle to the end of haystack. 
     *
     * @param   string substr
     * @param   bool cs default TRUE whether to check case-sensitively
     * @return  &text.String or NULL if substr is not found
     * @see     php://strstr
     */
    public function substringAfter($substr, $cs= TRUE) {
      if (FALSE === ($s= ($cs 
        ? strstr($this->buffer, $substr)
        : stristr($this->buffer, $substr)
      ))) return NULL;

      return new String($s);
    }
    
    /**
     * Returns a new string that is a substring of this string.
     *
     * @param   int begin
     * @param   int end default -1
     * @return  &text.String
     * @see     php://substr
     */
    public function substring($begin, $end= -1) {
      return new String(substr($this->buffer, $begin, $end));
    }

    /**
     * Returns a new string that is a substring of this string.
     *
     * @param   int begin
     * @param   int end default -1
     * @return  string
     * @see     php://substr
     */
    public function substr($begin, $end= -1) {
      return substr($this->buffer, $begin, $end);
    }
    
    /**
     * Concatenates the specified string to the end of this string
     * and returns a new string containing the result.
     *
     * @param   &text.String string
     * @return  &text.String a new string
     */
    public function concat($string) {
      return new String($this->buffer.$string->buffer);
    }
    
    /**
     * Concatenates the specified string to the end of this string,
     * changing this string.
     *
     * @param   &text.String string
     */
    public function append($string) {
      $this->buffer.= $string->buffer;
    }
    
    /**
     * Replaces search value(s) with replacement value(s) in this string
     *
     * @param   mixed search
     * @param   mixed replace
     * @see     php://str_replace
     */
    public function replace($search, $replace) {
      $this->buffer= str_replace($search, $replace, $this->buffer);
    }
    
    /**
     * Replaces pairs in this this string
     *
     * @param   array pairs an associative array, where keys are replaced by values
     * @see     php://strtr
     */
    public function replacePairs($pairs) {
      $this->buffer= strtr($search, $pairs);
    }
    
    /**
     * Delete a specified amount of characters from this string as
     * of a specified position.
     *
     * @param   int pos
     * @param   int len default 1
     */
    public function delete($pos, $len= 1) {
      $this->buffer= substr($this->buffer, 0, $pos).substr($this->buffer, $pos+ 1);
    }
    
    /**
     * Insert a substring into this string at a specified position. 
     *
     * @param   int pos
     * @param   string substring
     */
    public function insert($pos, $substring) {
      $this->buffer= substr($this->buffer, 0, $pos).$substring.substr($this->buffer, $pos);
    }
    
    /**
     * Tells whether or not this string matches the given regular expression.
     *
     * @param   string regex
     * @return  bool
     * @see     php://preg_match
     */
    public function matches($regex) {
      return preg_match($regex, $this->buffer);
    }
    
    /**
     * Split this string into portions delimited by separator
     *
     * @param   string separator
     * @param   int limit default 0
     * @return  &text.String[]
     * @see     php://explode
     */
    public function explode($separator, $limit= 0) {
      for (
        $a= ($limit 
          ? explode($separator, $this->buffer) 
          : explode($separator, $this->buffer, $limit)
        ), $s= sizeof($a), $i= 0; 
        $i < $s; 
        $i++
      ) {
        $a[$i]= new String($a[$i]);
      }
      return $a;
    }

    /**
     * Split this string into portions delimited by separator regex
     *
     * @param   string separator
     * @param   int limit default 0
     * @return  &text.String[]
     * @see     php://preg_split
     */
    public function split($separator, $limit= 0) {
      for (
        $a= ($limit 
          ? preg_split($separator, $this->buffer) 
          : preg_split($separator, $this->buffer, $limit)
        ), $s= sizeof($a), $i= 0; 
        $i < $s; 
        $i++
      ) {
        $a[$i]= new String($a[$i]);
      }
      return $a;
    }
    
    /**
     * Pad this string to a certain length with another string
     *
     * @param   int length
     * @param   string str default ' '
     * @param   int type default STR_PAD_RIGHT
     * @see     php://str_pad
     */
    public function pad($length, $str= ' ', $type= STR_PAD_RIGHT) {
      $this->buffer= str_pad($this->buffer, $length, $str, $type);
    }
    
    /**
     * Strip whitespace from the beginning and end of this string.
     *
     * If the parameter charlist is omitted, these characters will
     * be stripped:
     * <ul>
     *   <li>" " (ASCII 32 (0x20)), an ordinary space.</li>
     *   <li>"\t" (ASCII 9 (0x09)), a tab.</li>
     *   <li>"\n" (ASCII 10 (0x0A)), a new line (line feed).</li>
     *   <li>"\r" (ASCII 13 (0x0D)), a carriage return.</li>
     *   <li>"\0" (ASCII 0 (0x00)), the NUL-byte.</li>
     *   <li>"\x0B" (ASCII 11 (0x0B)), a vertical tab. </li>
     * </ul>
     *
     * @param   string charlist default NULL
     * @see     php://trim
     */
    public function trim($charlist= NULL) {
      if ($charlist) {
        $this->buffer= trim($this->buffer, $charlist);
      } else {
        $this->buffer= trim($this->buffer);
      }
    }

    /**
     * Strip whitespace from the beginning of this string.
     *
     * @param   string charlist default NULL
     * @see     php://ltrim
     * @see     xp://text.String#trim
     */
    public function ltrim($charlist= NULL) {
      if ($charlist) {
        $this->buffer= ltrim($this->buffer, $charlist);
      } else {
        $this->buffer= ltrim($this->buffer);
      }
    }

    /**
     * Strip whitespace from the end of this string.
     *
     * @param   string charlist default NULL
     * @see     php://ltrim
     * @see     xp://text.String#trim
     */
    public function rtrim($charlist= NULL) {
      if ($charlist) {
        $this->buffer= rtrim($this->buffer, $charlist);
      } else {
        $this->buffer= rtrim($this->buffer);
      }
    }
    
    /**
     * Converts all of the characters in this string to upper case using 
     * the rules of the current locale.
     *
     * @see     php://strtoupper
     * @return  &text.String this string
     */
    public function toUpperCase() {
      $this->buffer= strtoupper($this->buffer);
      return $this;
    }

    /**
     * Converts all of the characters in this string to lower case using 
     * the rules of the current locale.
     *
     * @see     php://strtolower
     * @return  &text.String this string
     */
    public function toLowerCase() {
      $this->buffer= strtolower($this->buffer);
      return $this;
    }
    
    /**
     * Parses input from this string according to a format
     *
     * @param   string format
     * @return  array
     * @see     php://sscanf
     */
    public function scan($format) {
      return sscanf($this->buffer, $format);
    }
    
    /**
     * Returns an array of strings
     *
     * Examples:
     * <code>
     *   $s= &new String('Hello');
     *   $a= $s->toArray();         // array('H', 'e', 'l', 'l', 'o')
     *
     *   $s= &new String('Friebe,Timm');
     *   $a= $s->toArray(',');      // array('Friebe', 'Timm')
     * </code>
     *
     * @param   string delim default ''
     * @return  string[]
     */
    public function toArray($delim= '') {
      if ($delim) return explode($delim, $this->buffer);
      
      $a= array();
      for ($i= 0, $s= strlen($this->buffer); $i < $s; $i++) {
        $a[]= $this->buffer{$i};
      }
      return $a;
    }
    
    /**
     * Creates a new string from an array, imploding it using the 
     * specified delimiter.
     *
     * Examples:
     * <code>
     *   $s= &String::fromArray(array('a', 'b', 'c'));  // "abc"
     *   $s= &String::fromArray(array(1, 2, 3), ',');   // "1,2,3"
     * </code>
     *
     * @param   string delim default ''
     * @return  &text.String string
     */
    public static function fromArray($arr, $delim= '') {
      return new String(implode($delim, $arr));
    }
    
    /**
     * Returns the string representation of the given argument. Calls the
     * toString() method on objects and implode() on arrays.
     *
     * @param   mixed arg
     * @return  &text.String string
     */
    public static function valueOf($arg) {
      if (is('Generic', $arg)) {
        return new String($arg->toString());
      } else if (is_array($arg)) {
        return new String(implode('', $arg));
      }
      return new String(strval($arg));
    }

    /**
     * Returns a string representation of this string.
     *
     * @see     xp://text.String#getBuffer
     * @return  string
     */
    public function toString() {
      return $this->buffer;
    }
  }
?>
