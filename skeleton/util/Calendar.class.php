<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  define('CAL_SEC_HOUR',    3600);
  define('CAL_SEC_DAY',     86400);
  define('CAL_SEC_WEEK',    604800);
  
  define('CAL_DST_EU',      0x0000);
  define('CAL_DST_US',      0x0001);
  
  uses('util.Date');
  
  /**
   * Calendar class
   *
   * @model   static
   */
  class Calendar extends Object {

    /**
     * Calculates start of DST (daylight savings time)
     * This is the last Sunday of March for Europe, the first Sunday of 
     * April in the U.S.
     *
     * @access  public
     * @param   int year default -1 Year, defaults to current year
     * @param   int method default CAL_DST_EU Method to calculate (CAL_DST_EU|CAL_DST_US)
     * @return  util.Date
     */
    function &dstBegin($year= -1, $method= CAL_DST_EU) {
      if (-1 == $year) $year= date('Y');
      $i= 0;
      $day= ($method == CAL_DST_US) ? 1 : 0;
      $ofs= ($method == CAL_DST_US) ? 1 : -1;
      do {
        $w= date('w', $m= mktime(0, 0, $day, 4, $i, $year));
        $i+= $ofs;
      } while ($w > 0);
      return new Date($m);
    }
  
    /**
     * Calculates end of DST (daylight savings time)
     * This is the last Sunday of October
     *
     * @access  public
     * @param   int year default -1 Year, defaults to current year
     * @return  util.Date
     */
    function &dstEnd($year= -1) {
      if (-1 == $year) $year= date('Y');
      $i= 0;
      do {
        $w= date('w', $m= mktime(0, 0, 0, 11, $i--, $year));
      } while ($w > 0);
      return new Date($m);
    }
  
    /**
     * Calculates the amount of workdays between to dates
     * Workdays are defined as Monday through Friday
     *
     * @access  public
     * @param   mixed start Startdate (either unix-Timestamp or util.Date)
     * @param   mixed end Enddate (either unix-Timestamp or util.Date)
     * @param   array holidays default array() Holidays to be included in calculation
     *          Format for array is: array(Unix-timestamp (create w/ gmmktime()!) => TRUE)
     * @return  int number of workdays
     */
    function workdays($start, $end, $holidays= array()) {
      $s= is_a($start, 'Date') ? $start->getTime() : $start;
      $e= is_a($end, 'Date') ? $end->getTime() : $end;

      // For holidays, we have to compare to midnight
      // else, don't calculate this
      if (!empty($holidays)) $s-= $s % CAL_SEC_DAY;
      
      // Is there a more intelligent way of doing this?
      $diff= floor(($e - $s) / CAL_SEC_DAY);
      for ($i= $s; $i <= $e; $i+= CAL_SEC_DAY) {
        $diff-= ((date('w', $i)+ 6) % 7 > 4 or isset($holidays[$i]));
      }
      
      return $diff+ 1;
    }
  
    /**
     * Returns calendar week for a day
     *
     * @access  public
     * @param   mixed date default -1 Date, defaults to today (either unix-Timestamp or util.Date)
     * @return  int calendar week
     * @see     http://www.salesianer.de/util/kalwoch.html 
     */
    function week($date= -1) {
      function caldiff($date, $year) {
        $d4= mktime(0, 0, 0, 1, 4, $year);
        return floor(1.05 + ($date- $d4) / CAL_SEC_WEEK+ ((date('w', $d4)+ 6) % 7) / 7);
      }
      
      // Check for passed arguments
      $d= (is_a($date, 'Date') 
        ? $date->getTime()
        : ($date == -1 ? time() : $date)
      );
      
      $year= date('Y', $d)+ 1;
      do {
        $w= caldiff($d, $year);
        $year--;
      } while ($w < 1);
      return $w;
    }
    
    /**
     * Get first of advent for given year
     *
     * @access  public
     * @param   int year default -1 Year, defaults to this year
     * @return    int Unix-timestamp for date of the first of advent
     * @see     http://www.salesianer.de/util/kalfaq.html
     */
    function advent($year= -1) {
      if ($year == -1) $year= date('Y');
     
      $s= mktime(0, 0, 0, 11, 26, $year);
      while (0 != date('w', $s)) $s+= CAL_SEC_DAY;
      return $s;
    }
    
    /**
     * Get easter date for given year
     *
     * @access  public
     * @param   int year default -1 Year, defaults to this year
     * @return    int Unix-timestamp for Easter date
     * @see        http://www.koenigsmuenster.de/rsk/epakte.htm
     * @see     http://www.salesianer.de/util/kalfaq.html
     * @see     php://easter-date#user_contrib
     */
    function easter($year= -1) {
      if ($year == -1) $year= date('Y');
      
      $g = $year % 19;
      $c = (int)($year / 100);
      $h = (int)($c - ($c / 4) - ((8*  $c + 13) / 25) + 19 * $g + 15) % 30;
      $i = (int)$h - (int)($h / 28) * (1 - (int)($h / 28)* (int)(29 / ($h+ 1)) * ((int)(21 - $g) / 11));
      $j = ($year + (int)($year / 4) + $i + 2 - $c + (int)($c / 4)) % 7;
      $l = $i - $j;
      $m = 3 + (int)(($l + 40) / 44);
      $d = $l + 28 - 31 * ((int)($m / 4));
      return mktime(0, 0, 0, $m, $d, $year);
    }
  }

?>
