<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.IllegalArgumentException',
    'lang.SystemException'
  );

  // Bootstring parameters for Punycode
  define('PUNYCODE_BASE',           36);
  define('PUNYCODE_TMIN',            1);
  define('PUNYCODE_TMAX',           26);
  define('PUNYCODE_SKEW',           38);
  define('PUNYCODE_DAMP',          700);
  define('PUNYCODE_INITIAL_BIAS',   72);
  define('PUNYCODE_INITIAL_N',    0x80);
  define('PUNYCODE_DELIMITER',    0x2d);

  /**
   * Implemented in PHP using punycode.c from RFC 3492
   * http://rfc-editor.org/rfc/rfc3492.txt
   *
   * punycode.c:
   * http://www.nicemice.net/idn/
   * Adam M. Costello
   * http://www.nicemice.net/amc/
   *
   * This is PHP code implementing Punycode (RFC 3492).
   *
   * @ext      iconv
   * @purpose  Punycode encoding/decoding
   */
  class PunyCode extends Object {

    /**
     * Return valid ASCII characters for Punycode
     *
     * @return  string
     * @see rfc://3492
     */
    public function getASCII() {
      static $ascii;
      $ascii =
        "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n".
        "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n".
        " !\"#$%&'()*+,-./".
        "0123456789:;<=>?".
        "@ABCDEFGHIJKLMNO".
        "PQRSTUVWXYZ[\\]^_".
        "`abcdefghijklmno".
        "pqrstuvwxyz{|}~\n";
      return $ascii;
    }
    
    /**
     * Bias adaptation function 
     *
     * @param   int delta
     * @param   int numpoints
     * @param   bool firsttime
     * @return  int
     * @see rfc://3492#6.1
     */
    protected function _adapt($delta, $numpoints, $firsttime) {
      $delta = $firsttime ? (int)($delta / PUNYCODE_DAMP) : $delta >> 1;
      // delta >> 1 is a faster way of doing delta / 2
      $delta += (int)($delta / $numpoints);

      for ($k= 0; $delta > ((PUNYCODE_BASE - PUNYCODE_TMIN) * PUNYCODE_TMAX) / 2;  $k += PUNYCODE_BASE) {
        $delta = (int)($delta / (PUNYCODE_BASE - PUNYCODE_TMIN));
      }
      return (int)($k + (PUNYCODE_BASE - PUNYCODE_TMIN + 1) * $delta / ($delta + PUNYCODE_SKEW));
    }

    /**
     * Original comment from author:
     * decode_digit(cp) returns the numeric value of a basic code
     * point (for use in representing integers) in the range 0 to
     * base-1, or base if cp is does not represent a value.
     *
     * @param  int cp
     * @return int
     * @see rfc://3492#5
     */
    protected function _decode_digit($cp) {
      return
        $cp - 48 < 10 ? $cp - 22 :  ($cp - 65 < 26 ? $cp - 65 :
        $cp - 97 < 26 ? $cp - 97 :  PUNYCODE_BASE);
    }

    /**
     * Does the following character mapping:
     *    0..25 map to ASCII a..z or A..Z 
     *   26..35 map to ASCII 0..9         
     *
     * @param  int d
     * @param  bool flag
     * @return int
     */
    protected function _encode_digit($d, $flag) {
      return $d + 22 + 75 * ($d < 26) - (($flag != 0) << 5);
    }

    /**
     * Encoding digits.
     *
     * @param  int  d
     * @param  bool flag
     * @return int
     * @see rfc://3492#5
     */
    protected function _encode_basic($bcp, $flag) {
      $bcp= $bcp;
      $bcp -= ($bcp - 97 < 26) << 5;
      return $bcp + ((!$flag && ($bcp - 65 < 26)) << 5);
    }

    /**
     * Orignal comment from author:
     * flagged(bcp) tests whether a basic code point is flagged
     * (uppercase).  The behavior is undefined if bcp is not a
     * basic code point.
     *
     * @param  int bcp
     * @return int
     */
    protected function _flagged($bcp) {
      return ord($bcp) - 65 < 26;
    }

    /**
     * Decode Punycode string and return TRUE on success.
     *
     * @param  string input  The punycode string
     * @param  &string result The result ASCII string
     * @param  &array flags  The flags for each character (see _flagged() function)
     * @return bool
     * @throws lang.IllegalArgumentException in case $input is not a punycode string
     * @throws lang.SystemException in case there's an interger overflow
     */
    public function decode($input, $result, $flags) {
      $in_len= strlen($input);
      $n= PUNYCODE_INITIAL_N;
      $out= $i= 0;
      $bias = PUNYCODE_INITIAL_BIAS;
      $output= $flags= array();
      $result= NULL;

      // Check for ASCII characters
      for ($b= 0; $b<$in_len; $b++) {
        if (strpos($this->getASCII(), $input[$b]) === FALSE) {
          throw new IllegalArgumentException('Input is not valid punycode');
        }
      }

      // Handle the basic code points:  Let b be the number of input code
      // points before the last delimiter, or 0 if there is none, then
      // copy the first b code points to the output.
      for ($b= $j= 0; $j < $in_len; ++$j) {
        if (PUNYCODE_DELIMITER == ord($input[$j])) $b = $j;
      }

      for ($j= 0; $j < $b; ++$j) {
        if ($flags !== NULL) $flags[$out] = $this->_flagged($input[$j]);
        if (ord($input[$j]) >= 0x80) {
          throw new IllegalArgumentException('Input is not valid punycode');
        }
        $output[$out++] = ord($input[$j]);
      }

      // Main decoding loop:  Start just after the last delimiter if any
      // basic code points were copied; start at the beginning otherwise.
      for ($in = $b > 0 ? $b + 1 : 0;  $in < $in_len; ++$out) {

        // in is the index of the next character to be consumed, and
        // out is the number of code points in the output array.

        // Decode a generalized variable-length integer into delta,
        // which gets added to i.  The overflow checking is easier
        // if we increase i as we go, then subtract off its starting
        // value at the end to obtain delta.
        for ($oldi = $i, $w = 1, $k = PUNYCODE_BASE; ; $k += PUNYCODE_BASE) {
          if ($in >= $in_len) {
            throw new IllegalArgumentException('Input is not valid punycode');
          }
          $digit = $this->_decode_digit(ord($input[$in++]));
          if ($digit >= PUNYCODE_BASE) {
            throw new IllegalArgumentException('Input is not valid punycode');
          }
          if ($digit > (LONG_MAX - $i) / $w) {
            throw new SystemException('Integer overflow');
          }
          $i += $digit * $w;
          $t =
            $k <= $bias ? PUNYCODE_TMIN :     // +tmin not needed
            ($k >= $bias + PUNYCODE_TMAX ? PUNYCODE_TMAX : $k - $bias);
          if ($digit < $t) break;
          if ($w > LONG_MAX / (PUNYCODE_BASE - $t)) {
            throw new SystemException('Integer overflow');
          }
          $w *= (PUNYCODE_BASE - $t);
        }

        $bias = $this->_adapt($i - $oldi, $out + 1, $oldi == 0);

        // i was supposed to wrap around from out+1 to 0,
        // incrementing n each time, so we'll fix that now:
        if ($i / ($out + 1) > LONG_MAX - $n) {
          throw new SystemException('Integer overflow');
        }
        $n += (int)($i / ($out + 1));
        $i %= ($out + 1);

        // Insert n at position i of the output:
        if ($flags !== NULL) {
          for ($x= ($out - $i) - 1; $x >= 0; $x--) $flags[$x+$i+1] = $flags[$x+$i];
          $flags[$i]= $this->_flagged($input[$in-1]);
        }

        for ($x= ($out - $i) - 1; $x >= 0; $x--) $output[$x+$i+1] = $output[$x+$i];
        $output[$i]= $n;
        $i++;
      }
      // Transform it to UCS-4 string
      $result= '';
      foreach ($output as $v) {
        $result.= chr(($v >> 24) & 255);
        $result.= chr(($v >> 16) & 255);
        $result.= chr(($v >>  8) & 255);
        $result.= chr($v         & 255);
      }
      return TRUE;
    }

    /**
     * Encode ASCII string to Punycode string and return TRUE on success.
     *
     * @param  string input  The ASCII string 
     * @param  &string result The result punycode string
     * @param  array flags  The flags for each character (see _flagged() function)
     * @return bool
     * @throws lang.IllegalArgumentException in case $input is not a punycode string
     * @throws lang.SystemException in case there's an interger overflow
     */
    public function encode($input, $result, $flags) {
      $in_len= strlen($input);
      $n = PUNYCODE_INITIAL_N;
      $delta = $out = 0;
      $bias = PUNYCODE_INITIAL_BIAS;
      $output= array();
      $result= NULL;

      // Handle the basic code points:
      for ($j= 0; $j < $in_len; ++$j) {
        if (ord($input[$j]) < 0x80) {
          $output[$out++] =
            chr(isset($flags[$j]) ? $this->_encode_basic(ord($input[$j]), $flags[$j]) : $input[$j]);
        }
      }

      $h = $b = $out;

      // h is the number of code points that have been handled, b is the
      // number of basic code points, and out is the number of characters
      // that have been output.

      if ($b > 0) $output[$out++] = chr(PUNYCODE_DELIMITER);

      // Main encoding loop:

      while ($h < $in_len) {
        // All non-basic code points < n have been
        // handled already.  Find the next larger one:

        for ($m= LONG_MAX, $j= 0; $j < $in_len; ++$j) {
          // if (basic(input[j])) continue;
          // (not needed for Punycode)
          if ((ord($input[$j]) >= $n) && (ord($input[$j]) < $m)) $m = ord($input[$j]);
        }

        // Increase delta enough to advance the decoder's
        // <n,i> state to <m,0>, but guard against overflow:

        if ($m - $n > (LONG_MAX - $delta) / ($h + 1)) {
          throw new SystemException('Integer overflow');
        }
        $delta += ($m - $n) * ($h + 1);
        $n = $m;

        for ($j= 0; $j < $in_len; ++$j) {
          // Punycode does not need to check whether input[j] is basic:
          if (ord($input[$j]) < $n) {
            if (++$delta == 0) {
              throw new SystemException('Integer overflow');
            }
          }

          if (ord($input[$j]) == $n) {
            // Represent delta as a generalized variable-length integer:

            for ($q= $delta, $k= PUNYCODE_BASE;  ; $k += PUNYCODE_BASE) {
              $t =
                $k <= $bias ? PUNYCODE_TMIN :     // +tmin not needed
                ($k >= $bias + PUNYCODE_TMAX ? PUNYCODE_TMAX : $k - $bias);
              if ($q < $t) break;
              $output[$out++] = chr($this->_encode_digit($t + ($q - $t) % (PUNYCODE_BASE - $t), 0));
              $q = (int)($q - $t) / (PUNYCODE_BASE - $t);
            }

            $output[$out++] = chr($this->_encode_digit($q, isset($flags[$j]) && $flags[$j]));
            $bias = $this->_adapt($delta, $h + 1, $h == $b);
            $delta = 0;
            ++$h;
          }
        }

        ++$delta;
        ++$n;
      }

      $result= implode('', $output);
      return TRUE;
    }
    
    /**
     * Decode Punycode string and return TRUE on success.
     *
     * @param   string str The Punycode string
     * @return  bool
     * @throws  lang.XPException from _decode()
     */
    public function decodeString($str, $charset= 'ISO-8859-1') {
      $out= '';
      $flags= array();
      $p= new PunyCode();
      $p->decode($str, $out, $flags);
      if ($charset != 'UCS-4') {
        if (($out= iconv('UCS-4', $charset, $out)) === FALSE) {
          throw new XPException('Can not convert string to requested encoding('.$charset.')');
        }
      }
      return $out;
    }

    /**
     * Encode ASCII string to Punycode string and return TRUE on success.
     *
     * @param   string str The ASCII string
     * @return  bool
     * @throws  lang.XPException from _encode()
     */
    public function encodeString($str) {
      $out= '';
      $flags= array_fill(0, strlen($str)+ 1, FALSE);
      array_pop($flags);
      $p= new PunyCode();
      $p->encode($str, $out, $flags);
      return $out;
    }
  }
?>
