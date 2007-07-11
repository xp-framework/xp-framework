<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.SQLFunction', 'rdbms.SQLDialect', 'lang.IllegalArgumentException');
  
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
     * @param   mixed col1 string or rdbms.SQLFunction
     * @param   mixed col2 string or rdbms.SQLFunction
     * @param   mixed col3 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function substring() {
      $f= new SQLFunction('substring', '%s');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * Returns the starting position of the first occurrence of col1
     * within col2. If the optional col3 is specified, it
     * indicates the character position in col2 at which the
     * search is to begin. If col1 is not found within col2, the value 0 is returned.
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @param   mixed col2 string or rdbms.SQLFunction
     * @param   mixed col3 string or rdbms.SQLFunction optional
     * @return  rdbms.SQLFunction
     */
    public static function locate() {
      $f= new SQLFunction('locate', '%d');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * cut off leading and trailing chars given ba col2
     * if col2 is omitted, whitespaces are cutted off
     * 
     * @param   mixed* args string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function trim() {
      $f= new SQLFunction('trim', '%s');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * cut off leading chars given ba col2
     * if col2 is omitted, whitespaces are cutted off
     *
     * @param   mixed* args string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function ltrim() {
      $f= new SQLFunction('ltrim', '%s');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * cut off trailing chars given ba col2
     * if col2 is omitted, whitespaces are cutted off
     *
     * @param   mixed* args string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function rtrim() {
      $f= new SQLFunction('rtrim', '%s');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * get the length of col1
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function length($col) {
      return new SQLFunction('length', '%d', array($col));
    }

    /**
     * get the length of col1 in bit
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function bitLength($col) {
      return new SQLFunction('bit_length', '%d', array($col));
    }

    /**
     * get col1 in upper case
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function upper($col) {
      return new SQLFunction('upper', '%s', array($col));
    }

    /**
     * get col1 in lower case
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function lower($col) {
      return new SQLFunction('lower', '%s', array($col));
    }

    /**
     * get the ascii value for a char
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function ascii($col1) {
      return new SQLFunction('ascii', '%s', array($col1));
    }

    /**
     * get the char for an ascii value
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function char($col1) {
      return new SQLFunction('char', '%s', array($col1));
    }

    /**
     * get the length of a string
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function len($col1) {
      return new SQLFunction('len', '%d', array($col1));
    }

    /**
     * turn a string backwards
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function reverse($col1) {
      return new SQLFunction('reverse', '%s', array($col1));
    }

    /**
     * get col1 whitespaces
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function space($col1) {
      return new SQLFunction('space', '%s', array($col1));
    }

    /**
     * concatinates all parameters
     * two arguments or more
     *
     * @param   mixed* args string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function concat() {
      $f= new SQLFunction('concat', '%s');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * Returns a four-character soundex code for
     * character strings
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function soundex($val) {
      return new SQLFunction('soundex', '%s', array($val));
    }

    /**
     * extract seconds from a date field
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function second($col) {
      return new SQLFunction('second', '%d', array($col));
    }

    /**
     * extract minutes from a date field
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function minute($col) {
      return new SQLFunction('minute', '%d', array($col));
    }

    /**
     * extract hours from a date field
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function hour($col) {
      return new SQLFunction('hour', '%d', array($col));
    }

    /**
     * extract days from a date field
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function day($col) {
      return new SQLFunction('day', '%d', array($col));
    }

    /**
     * extract months from a date field
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function month($col) {
      return new SQLFunction('month', '%d', array($col));
    }

    /**
     * extract years from a date field
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function year($col) {
      return new SQLFunction('year', '%d', array($col));
    }

    /**
     * get the current date
     *
     * @return  rdbms.SQLFunction
     */
    public static function getdate() {
      return new SQLFunction('getdate', '%s');
    }

    /**
     * add the $offset to a certain $datepart of $date
     * e.g. dateadd('month', 4, getdate()) will return a date 4 months in the future
     *
     * @param   mixed datepart string or rdbms.SQLFunction
     * @param   mixed offset string or rdbms.SQLFunction
     * @param   mixed date string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function dateadd($datepart, $offset, $date) {
      return new SQLFunction('dateadd', '%s', array($datepart, $offset, $date));
    }

    /**
     * get the difference of date1 and date2 in "type"
     *
     * @param   mixed type string or rdbms.SQLFunction
     * @param   mixed date1 string or rdbms.SQLFunction
     * @param   mixed date2 string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function datediff($type, $date1, $date2) {
      return new SQLFunction('datediff', '%d', array($type, $date1, $date2));
    }

    /**
     * produces the specified datepart of
     * the specified date as a character string.
     *
     * @param   mixed type string or rdbms.SQLFunction
     * @param   mixed date string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function datename($type, $date) {
      return new SQLFunction('datename', '%s', array($type, $date));
    }

    /**
     * produces the specified datepart of
     * the specified date as an integer
     *
     * @param   mixed type string or rdbms.SQLFunction
     * @param   mixed date string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function datepart($type, $date) {
      return new SQLFunction('datepart', '%s', array($type, $date));
    }

    /**
     * get the absolut amount of col1
     * 
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function abs($col) {
      return new SQLFunction('abs', '%d', array($col));
    }

    /**
     * get the angle (in radians) whose cosine
     * is the specified value.
     * 
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function acos($col) {
      return new SQLFunction('acos', '%f', array($col));
    }

    /**
     * get the angle (in radians) whose sine
     * is the specified value.
     * 
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function asin($col) {
      return new SQLFunction('asin', '%f', array($col));
    }

    /**
     * get the angle (in radians) whose tangent
     * is the specified value.
     * 
     * @param   mixed* args string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function atan() {
      $f= new SQLFunction('atan', '%f');
      $f->args= func_get_args();
      return $f;
    }

    /**
     * get the smallest integer greater than or
     * equal to the specified value.
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function ceil($val) {
      return new SQLFunction('ceil', '%d', array($val));
    }

    /**
     * Returns the cosine of the specified angle
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function cos($val) {
      return new SQLFunction('cos', '%f', array($val));
    }

    /**
     * Returns the cotangent of the specified angle
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function cot($val) {
      return new SQLFunction('cot', '%f', array($val));
    }

    /**
     * Converts radians to degrees.
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function degrees($val) {
      return new SQLFunction('degrees', '%f', array($val));
    }

    /**
     * Returns the exponential value of the
     * specified value..
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function exp($val) {
      return new SQLFunction('exp', '%f', array($val));
    }

    /**
     * Returns the largest integer less than or equal
     * to the specified value.
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function floor($val) {
      return new SQLFunction('floor', '%d', array($val));
    }

    /**
     * Returns the natural logarithm of the
     * specified value.
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function log($val) {
      return new SQLFunction('log', '%f', array($val));
    }

    /**
     * Returns the base 10 logarithm of the
     * specified value.
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function log10($val) {
      return new SQLFunction('log10', '%f', array($val));
    }

    /**
     * Returns PI
     * 
     * @return  rdbms.SQLFunction
     */
    public static function pi() {
      return new SQLFunction('pi', '%f');
    }

    /**
     * return $val in the power of $power
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @param   mixed power string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function power($val, $power) {
      return new SQLFunction('power', '%f', array($val, $power));
    }

    /**
     * Converts degrees to radians. Results are of
     * the same type as numeric.
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function radians($val) {
      return new SQLFunction('radians', '%f', array($val));
    }

    /**
     * Returns a random float value between 0 and 1
     * 
     * @return  rdbms.SQLFunction
     */
    public static function rand() {
      return new SQLFunction('rand', '%f');
    }

    /**
     * Rounds the numeric so that it has integer
     * significant digits. A positive integer
     * determines the number of significant digits
     * 
     * @param   mixed val string or rdbms.SQLFunction
     * @param   mixed precision string or rdbms.SQLFunction optional default 0
     * @return  rdbms.SQLFunction
     */
    public static function round($val, $precision= 0) {
      return new SQLFunction('round', '%f', array($val, $precision));
    }

    /**
     * Returns the positive (+1), zero (0), or
     * negative (-1).
     * 
     * @param   mixed val string or rdbms.SQLFunction optional default 0
     * @return  rdbms.SQLFunction
     */
    public static function sign($val) {
      return new SQLFunction('sign', '%d', array($val));
    }

    /**
     * Returns the sine of the specified angle
     * 
     * @param   mixed val string or rdbms.SQLFunction optional default 0
     * @return  rdbms.SQLFunction
     */
    public static function sin($val) {
      return new SQLFunction('sin', '%f', array($val));
    }

    /**
     * get the square root of col1
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function sqrt($col) {
      return new SQLFunction('sqrt', '%f', array($col));
    }

    /**
     * Returns the tangent of the specified angle
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function tan($col) {
      return new SQLFunction('tan', '%f', array($col));
    }

    /**
     * cast col1 to the datatype typename
     *
     * @param   mixed col1 string or rdbms.SQLFunction
     * @param   string typename
     * @return  rdbms.SQLFunction
     * @throws  lang.IllegalArgumentException
     */
    public static function cast($col1, $typename) {
      $typename= (strpos($typename, '(') === false) ? $typename : substr($typename, 0, strpos($typename, '('));
      static $datatypes= array(
        'bigint'     => '%d',
        'binary'     => '%s',
        'blob'       => '%s',
        'char'       => '%s',
        'clob'       => '%s',
        'date'       => '%s',
        'datetime'   => '%s',
        'dec'        => '%f',
        'decimal'    => '%f',
        'double'     => '%f',
        'float'      => '%f',
        'int'        => '%d',
        'integer'    => '%d',
        'smallint'   => '%d',
        'text'       => '%s',
        'time'       => '%s',
        'timestamp'  => '%s',
        'varbinary'  => '%s',
        'varchar'    => '%s',
      );
      if (!isset($datatypes[$typename])) throw new IllegalArgumentException($typename.': unknowen typename');
      return new SQLFunction('cast', $datatypes[$typename], array($col1, $typename));
    }

    /**
     * cast col1 to string
     *
     * @param   mixed col string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function str($col) {
      return new SQLFunction('str', '%s', array($col));
    }

    /**
     * get the first argument, that is not NULL
     * two arguments or more
     *
     * @param   mixed* args string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function coalesce() {
      $args= func_get_args();
      return new SQLFunction('coalesce', self::getTypeToken($args[0]), $args);
    }

    /**
     * return null if col1 equals col2, else col1
     *
     * @param   mixed string or rdbms.SQLFunction
     * @param   mixed string or rdbms.SQLFunction
     * @return  rdbms.SQLFunction
     */
    public static function nullif($col1, $col2) {
      return new SQLFunction('nullif', self::getTypeToken($col1), array($col1, $col2));
    }
    
    /**
     * get the type token for a value
     *
     * @param   mixed obj
     * @return  string typetoken for prepare
     * @throws  lang.IllegalArgumentException
     */
    private static function getTypeToken($obj) {
      if (is_numeric($obj))            return '%f';
      if (is_string($obj))             return '%s';
      if ($obj instanceof SQLFragment) return $obj->getType();
      throw new IllegalArgumentException('argument is from illegal type');
    }
  }
?>
