<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Abstract base class for TDS records
   *
   */
  abstract class TdsRecord extends Object {
    protected static $precision;

    static function __static() {
      self::$precision= ini_get('precision');
    }

    /**
     * Convert days and seconds since 01.01.1900 to a date
     *
     * @param   int days
     * @param   int seconds
     * @return  string
     */
    protected function toDate($days, $seconds) {
      return Date::create(1900, 1, 1 + $days, 0, 0, $seconds / 300);
    }

    /**
     * Convert lo and hi values to money value
     *
     * @param   int hi
     * @param   int lo
     * @return  double
     */
    protected function toMoney($hi, $lo= 0) {
      if ($hi < 0) {
        $hi= ~$hi;
        $lo= ~($lo - 1);
        $div= -10000;
      } else {
        $div= 10000;
      }
      return (double)bcdiv(bcadd(bcmul($hi, '4294967296'), $lo), $div, 5);
    }

    /**
     * Convert to number
     *
     * @param   string bytes
     * @return  var
     */
    protected function toDouble($bytes) {
      return current(unpack('d', $bytes));
    }

    /**
     * Convert to number
     *
     * @param   string bytes
     * @return  var
     */
    protected function toFloat($bytes) {
      return current(unpack('f', $bytes));
    }

    /**
     * Convert to number
     *
     * @param   string n
     * @param   int scale
     * @param   int prec
     * @return  var
     */
    protected function toNumber($n, $scale, $prec) {
      if (0 === $scale) {
        return bccomp($n, LONG_MAX) == 1 || bccomp($n, LONG_MIN) == -1 ? $n : (int)$n;
      } else {
        $n= bcdiv($n, pow(10, $scale), $scale);
        return strlen($n) > self::$precision ? $n : (double)$n;
      }
    }
    
    /**
     * Unmarshal from a given stream
     *
     * @param   rdbms.tds.TdsDataStream stream
     * @param   [:var] field
     * @param   [:self] records
     * @return  var
     */
    public abstract function unmarshal($stream, $field, $records);
  }
?>
