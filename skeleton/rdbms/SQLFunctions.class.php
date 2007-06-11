<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.SQLFunction', 'rdbms.SQLDialect');
  
  /**
   * use sql functions with different databases
   *
   * @test     xp://net.xp_framework.unittest.rdbms.SQLFunctionTest
   * @see      xp://rdbms.Criteria
   * @purpose  purpose
   */
  class SQLFunctions extends Object {

    /**
     * return a substring from col1, that start at
     * position given by col2 and is col3 chars long
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function substring($col1, $col1, $col2) {
      return new SQLFunction('substring', $col1, $col2);
    }

    /**
     * Returns the starting position of the first occurrence of col1
     * within col2. If the optional col3 is specified, it
     * indicates the character position in col2 at which the
     * search is to begin. If col1 is not found within col2, the value 0 is returned.
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction optional
     * @return  rdbms.SQLFunction
     */
    public static function locate() {
      $f= new SQLFunction('locate');
      $f->args=func_get_args();
      return $f;
    }

    /**
     * cut off leading and trailing chars given ba col2
     * if col2 is omitted, whitespaces are cutted off
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction optional
     * @return  rdbms.SQLFunction
     */
    public static function trim() {
      $f= new SQLFunction('trim');
      $f->args=func_get_args();
      return $f;
    }

    /**
     * cut off leading chars given ba col2
     * if col2 is omitted, whitespaces are cutted off
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction optional
     * @return  rdbms.SQLFunction
     */
    public static function ltrim() {
      $f= new SQLFunction('ltrim');
      $f->args=func_get_args();
      return $f;
    }

    /**
     * cut off trailing chars given ba col2
     * if col2 is omitted, whitespaces are cutted off
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction optional
     * @return  rdbms.SQLFunction
     */
    public static function rtrim() {
      $f= new SQLFunction('rtrim');
      $f->args=func_get_args();
      return $f;
    }

    /**
     * get the length of col1
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function length($col) {
      return new SQLFunction('length', $col);
    }

    /**
     * get the length of col1 in bit
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function bitLength($col) {
      return new SQLFunction('bit_length', $col);
    }

    /**
     * get col1 in upper case
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function upper($col) {
      return new SQLFunction('upper', $col);
    }

    /**
     * get col1 in lower case
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function lower($col) {
      return new SQLFunction('lower', $col);
    }

    /**
     * get the ascii value for a char
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function ascii($col1) {
      return new SQLFunction('ascii', $col1);
    }

    /**
     * get the char for an ascii value
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function char($col1) {
      return new SQLFunction('char', $col1);
    }

    /**
     * get the length of a string
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function len($col1) {
      return new SQLFunction('len', $col1);
    }

    /**
     * turn a string backwards
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function reverse($col1) {
      return new SQLFunction('reverse', $col1);
    }

    /**
     * get col1 whitespaces
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function space($col1) {
      return new SQLFunction('space', $col1);
    }

    /**
     * concatinates all parameters
     * two arguments or more
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function concat() {
      $f= new SQLFunction('concat');
      $f->args=func_get_args();
      return $f;
    }

    /**
     * Returns a four-character soundex code for
     * character strings
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function soundex($val) {
      return new SQLFunction('soundex', $val);
    }

    /**
     * extract seconds from a date field
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function second($col) {
      return new SQLFunction('second', $col);
    }

    /**
     * extract minutes from a date field
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function minute($col) {
      return new SQLFunction('minute', $col);
    }

    /**
     * extract hours from a date field
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function hour($col) {
      return new SQLFunction('hour', $col);
    }

    /**
     * extract days from a date field
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function day($col) {
      return new SQLFunction('day', $col);
    }

    /**
     * extract months from a date field
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function month($col) {
      return new SQLFunction('month', $col);
    }

    /**
     * extract years from a date field
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function year($col) {
      return new SQLFunction('year', $col);
    }

    /**
     * get the current date
     *
     * @return  rdbms.SQLFunction
     */
    public static function getdate() {
      return new SQLFunction('getdate');
    }

    /**
     * add the $offset to a certain $datepart of $date
     * e.g. dateadd(month, 4, getdate()) will return a date 4 months in the future
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function dateadd($datepart, $offset, $date) {
      return new SQLFunction('dateadd', $datepart, $offset, $date);
    }

    /**
     * get the difference of date1 and date2 in "type"
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function datediff($type, $date1, $date2) {
      return new SQLFunction('datediff', $type, $date1, $date2);
    }

    /**
     * produces the specified datepart of
     * the specified date as a character string.
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function datename($type, $date) {
      return new SQLFunction('datename', $type, $date);
    }

    /**
     * produces the specified datepart of
     * the specified date as an integer
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function datepart($type, $date) {
      return new SQLFunction('datepart', $type, $date);
    }

    /**
     * get the absolut amount of col1
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function abs($col) {
      return new SQLFunction('abs', $col);
    }

    /**
     * get the angle (in radians) whose cosine
     * is the specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function acos($col) {
      return new SQLFunction('acos', $col);
    }

    /**
     * get the angle (in radians) whose sine
     * is the specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function asin($col) {
      return new SQLFunction('asin', $col);
    }

    /**
     * get the angle (in radians) whose tangent
     * is the specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function atan() {
      $f= new SQLFunction('atan');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * get the smallest integer greater than or
     * equal to the specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function ceil($val) {
      return new SQLFunction('ceil', $val);
    }

    /**
     * Returns the cosine of the specified angle
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function cos($val) {
      return new SQLFunction('cos', $val);
    }

    /**
     * Returns the cotangent of the specified angle
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function cot($val) {
      return new SQLFunction('cot', $val);
    }

    /**
     * Converts radians to degrees.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function degrees($val) {
      return new SQLFunction('degrees', $val);
    }

    /**
     * Returns the exponential value of the
     * specified value..
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function exp($val) {
      return new SQLFunction('exp', $val);
    }

    /**
     * Returns the largest integer less than or equal
     * to the specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function floor($val) {
      return new SQLFunction('floor', $val);
    }

    /**
     * Returns the natural logarithm of the
     * specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function log($val) {
      return new SQLFunction('log', $val);
    }

    /**
     * Returns the base 10 logarithm of the
     * specified value.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function log10($val) {
      return new SQLFunction('log10', $val);
    }

    /**
     * Returns PI
     * 
     * @return  rdbms.SQLFunction
     */
    public static function pi() {
      return new SQLFunction('pi');
    }

    /**
     * return $val in the power of $power
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function power($val, $power) {
      return new SQLFunction('power', $val, $power);
    }

    /**
     * Converts degrees to radians. Results are of
     * the same type as numeric.
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function radians($val) {
      return new SQLFunction('radians', $val);
    }

    /**
     * Returns a random float value between 0 and 1
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function rand() {
      return new SQLFunction('rand');
    }

    /**
     * Rounds the numeric so that it has integer
     * significant digits. A positive integer
     * determines the number of significant digits
     * 
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction optional default 0
     * @return  rdbms.SQLFunction
     */
    public static function round($val, $precision= 0) {
      return new SQLFunction('round', $val, $precision);
    }

    /**
     * Returns the positive (+1), zero (0), or
     * negative (-1).
     * 
     * @param   mixed string or rdbms.SQLFunction optional default 0
     * @return  rdbms.SQLFunction
     */
    public static function sign($val) {
      return new SQLFunction('sign', $val);
    }

    /**
     * Returns the sine of the specified angle
     * 
     * @param   mixed string or rdbms.SQLFunction optional default 0
     * @return  rdbms.SQLFunction
     */
    public static function sin($val) {
      return new SQLFunction('sin', $val);
    }

    /**
     * get the square root of col1
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function sqrt($col) {
      return new SQLFunction('sqrt', $col);
    }

    /**
     * Returns the tangent of the specified angle
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function tan($col) {
      return new SQLFunction('tan', $col);
    }

    /**
     * cast col1 to the datatype col2
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function cast($col1, $col2) {
      return new SQLFunction('cast', $col1, $col2);
    }

    /**
     * cast col1 to string
     *
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function str($col) {
      return new SQLFunction('str', $col);
    }

    /**
     * get the first argument, that is not NULL
     * two arguments or more
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function coalesce() {
      $f= new SQLFunction('coalesce');
      $f->args=func_get_args();
      return $f;
    }

    /**
     * return null if col1 equals col2, else col1
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function nullif($col1, $col2) {
      return new SQLFunction('nullif', $col1, $col2);
    }

  }
?>
