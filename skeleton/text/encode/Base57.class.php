<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Encodes/decodes data with base57
   *
   * <code>
   *   $b= Base57::encode($number);
   *   $number= Base57::decode($b);
   * </code>
   *
   * @see      rfc://2045#6.8
   * @purpose  Base 57 encoder/decoder
   */
  class Base57 extends Object {
  
    /**
     * Encode number
     *
     * @model   static
     * @access  public
     * @param   int number
     * @return  string
     */
    function encode($number) {
      static $lookup= array( 
         0 => 'A', 17 => 'T', 34 => 'k', 51 => '4',
         1 => 'B', 18 => 'U', 35 => 'm', 52 => '5',
         2 => 'C', 19 => 'V', 36 => 'n', 53 => '6',
         3 => 'D', 20 => 'W', 37 => 'o', 54 => '7',
         4 => 'E', 21 => 'X', 38 => 'p', 55 => '8',
         5 => 'F', 22 => 'Y', 39 => 'q', 56 => '9',
         6 => 'G', 23 => 'Z', 40 => 'r',
         7 => 'H', 24 => 'a', 41 => 's',
         8 => 'J', 25 => 'b', 42 => 't',
         9 => 'K', 26 => 'c', 43 => 'u',
        10 => 'L', 27 => 'd', 44 => 'v',
        11 => 'M', 28 => 'e', 45 => 'w',
        12 => 'N', 29 => 'f', 46 => 'x',
        13 => 'P', 30 => 'g', 47 => 'y',
        14 => 'Q', 31 => 'h', 48 => 'z',
        15 => 'R', 32 => 'i', 49 => '2',
        16 => 'S', 33 => 'j', 50 => '3',
      );

      for ($out= '', $i= ceil(log($number, exp(1)) / log(57, exp(1)))- 1; $i >= 0; $i--) {
        $pow= pow(57, $i);
        $out.= $lookup[intval($number / $pow)];
        $number %= $pow;
      }
      return $out;
    }
    
    /**
     * Decode base57 encoded data
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  int
     */
    function decode($str) { 
      static $lookup= array( 
        'A' =>  0, 'T' => 17, 'k' => 34, '4' => 51,
        'B' =>  1, 'U' => 18, 'm' => 35, '5' => 52,
        'C' =>  2, 'V' => 19, 'n' => 36, '6' => 53,
        'D' =>  3, 'W' => 20, 'o' => 37, '7' => 54,
        'E' =>  4, 'X' => 21, 'p' => 38, '8' => 55,
        'F' =>  5, 'Y' => 22, 'q' => 39, '9' => 56,
        'G' =>  6, 'Z' => 23, 'r' => 40,
        'H' =>  7, 'a' => 24, 's' => 41,
        'J' =>  8, 'b' => 25, 't' => 42,
        'K' =>  9, 'c' => 26, 'u' => 43,
        'L' => 10, 'd' => 27, 'v' => 44,
        'M' => 11, 'e' => 28, 'w' => 45,
        'N' => 12, 'f' => 29, 'x' => 46,
        'P' => 13, 'g' => 30, 'y' => 47,
        'Q' => 14, 'h' => 31, 'z' => 48,
        'R' => 15, 'i' => 32, '2' => 49,
        'S' => 16, 'j' => 33, '3' => 50,
      );
      
      $number= 0;
      for ($i= 0, $s= strlen($str); $i < $s; $i++) {
        $number+= $lookup[$str{$i}] * pow(57, $s - $i - 1);
      }
      return $number;
    }
  }
?>
