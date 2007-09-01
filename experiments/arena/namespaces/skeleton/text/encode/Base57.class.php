<?php
/* This class is part of the XP framework
 *
 * $Id: Base57.class.php 10663 2007-06-26 10:29:30Z friebe $ 
 */

  namespace text::encode;
 
  define('BASE57_CHARTABLE',  'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789');

  /**
   * Encodes/decodes data with base57
   *
   * <code>
   *   $b= Base57::encode($number);
   *   $number= Base57::decode($b);
   * </code>
   *
   * @ext      bcmath
   * @purpose  Base 57 encoder/decoder
   */
  class Base57 extends lang::Object {
  
    /**
     * Encode number
     *
     * @param   int number
     * @return  string
     */
    public static function encode($number) {
      static $chars= BASE57_CHARTABLE;

      $prec= ini_set('precision', 20);

      $length= ceil(log($number, exp(1)) / log(57, exp(1)));
      for ($out= '', $i= 0; $i < $length; $i++) {
        $out= $chars{bcmod($number, 57)}.$out;
        $number= bcdiv($number, 57, 0);
      }
      
      ini_set('precision', $prec);
      return $out;      
    }
    
    /**
     * Decode base57 encoded data
     *
     * @param   string str
     * @return  int
     */
    public static function decode($str) { 
      static $chars= BASE57_CHARTABLE;

      $prec= ini_set('precision', 20);
      
      $number= 0;
      for ($i= 0, $s= strlen($str); $i < $s; $i++) {
        $number= bcadd(bcmul($number, 57), strpos($chars, $str{$i}));
      }

      ini_set('precision', $prec);
      return $number;
    }
  }
?>
