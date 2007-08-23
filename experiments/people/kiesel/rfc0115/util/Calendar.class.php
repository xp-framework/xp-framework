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
  
  uses(
    'util.Date', 
    'util.TimeZone', 
    'util.TimeZoneTransition'
  );
  
  /**
   * Calendar class
   *
   * @test     xp://net.xp_framework.unittest.DateTest
   * @purpose  Utility functions for date calculations
   */
  class Calendar extends Object {

    /**
     * Calculates start of DST (daylight savings time).
     *
     * This is the last Sunday of March for Europe, the first Sunday of 
     * April in the U.S.
     *
     * @param   mixed year default -1 Year, defaults to current year
     * @param   int method default CAL_DST_EU Method to calculate (CAL_DST_EU|CAL_DST_US)
     * @return  util.Date
     */
    public static function dstBegin($year= -1, $method= CAL_DST_EU) {
      switch (TRUE) {
        case $year instanceof Date: {
          $tz= $year->getTimeZone();
          $date= Date::create($year->getYear(), 1, 1, 0, 0, 0);
          break;
        }
        
        default: {
          switch ($method) {
            default:
            case CAL_DST_EU: $tz= new TimeZone('Europe/Berlin'); break;
            case CAL_DST_US: $tz= new TimeZone('America/New_York'); break;
          }
          
          $date= ($year == -1
            ? Date::now()
            : Date::create($year, 1, 1, 0, 0, 0)
          );
          break;
        }
      }
      
      $transition= TimeZoneTransition::nextTransition($tz, $date);
      while (!$transition->isDst()) { $transition->next(); }
      return $transition->getDate();
    }
  
    /**
     * Calculates end of DST (daylight savings time)
     * This is the last Sunday of October
     *
     * @param   int year default -1 Year, defaults to current year
     * @return  util.Date
     */
    public static function dstEnd($year= -1) {
      $date= ($year == -1
        ? Date::now()
        : Date::create($year, 1, 1, 0, 0, 0)
      );

      $transition= TimeZoneTransition::nextTransition(new TimeZone('Europe/Berlin'), $date);
      while ($transition->isDst()) { $transition->next(); }
      return $transition->getDate();
    }
    
    /**
     * Retrieve whether a given date object is in daylight savings time.
     *
     * @param   util.Date date
     * @param   int method default CAL_DST_EU Method to calculate (CAL_DST_EU|CAL_DST_US)
     * @return  bool
     */
    public static function inDst($date, $method= CAL_DST_EU) {
      return (
        $date->isAfter(Calendar::dstBegin($date->getYear(), $method)) &&
        $date->isBefore(Calendar::dstEnd($date->getYear()))
      );
    }
  
    /**
     * Calculates the amount of workdays between to dates. Workdays are 
     * defined as Monday through Friday.
     *
     * This method takes an optional argument, an array of the following
     * form:
     *
     * <code>
     *   $holidays[gmmktime(...)]= TRUE;
     * </code>
     *
     * @param   util.Date start
     * @param   util.Date end
     * @param   array holidays default array() holidays to be included in calculation
     * @return  int number of workdays
     */
    public static function workdays($start, $end, $holidays= array()) {
      $s= $start->getTime();
      $e= $end->getTime();

      // For holidays, we have to compare to midnight
      // else, don't calculate this
      if (!empty($holidays)) $s-= $s % CAL_SEC_DAY;
      
      // Is there a more intelligent way of doing this?
      $diff= floor(($e - $s) / CAL_SEC_DAY);
      for ($i= $s; $i <= $e; $i+= CAL_SEC_DAY) {
        $diff-= ((date('w', $i)+ 6) % 7 > 4 || isset($holidays[$i]));
      }
      
      return $diff+ 1;
    }
    
    /**
     * Return midnight of a given date
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function midnight($date) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        0,
        0,
        0
      );
    }
    
    /**
     * Return beginning of month for a given date. E.g., given a date
     * 2003-06-08, the function will return 2003-06-01 00:00:00.
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function monthBegin($date) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        1,
        0,
        0,
        0
      );
    }

    /**
     * Return end of month for a given date. E.g., given a date
     * 2003-06-08, the function will return 2003-06-30 23:59:59.
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function monthEnd($date) {
      return Date::create(
        $date->getYear(),
        $date->getMonth() + 1,
        0,
        23,
        59,
        59
      );
    }

    /**
     * Helper method for Calendar::week
     *
     * @param   int stamp
     * @param   int year
     * @return  int
     */
    protected static function caldiff($stamp, $year) {
      $d4= mktime(0, 0, 0, 1, 4, $year);
      return floor(1.05 + ($stamp- $d4) / CAL_SEC_WEEK+ ((date('w', $d4)+ 6) % 7) / 7);
    }
  
    /**
     * Returns calendar week for a day
     *
     * @param   util.Date date
     * @return  int calendar week
     * @see     http://www.salesianer.de/util/kalwoch.html 
     */
    public static function week($date) {
      $d= $date->getTime();
      $y= $date->getYear() + 1;
      do {
        $w= Calendar::caldiff($d, $y);
        $y--;
      } while ($w < 1);

      return (int)$w;
    }
    
    /**
     * Get first of advent for given year
     *
     * @param   int year default -1 year, defaults to this year
     * @return  util.Date for date of the first of advent
     * @see     http://www.salesianer.de/util/kalfaq.html
     */
    public static function advent($year= -1) {
      if (-1 == $year) $year= date('Y');
     
      $s= mktime(0, 0, 0, 11, 26, $year);
      while (0 != date('w', $s)) {
        $s+= CAL_SEC_DAY;
      }
      
      return new Date($s);
    }
    
    /**
     * Get easter date for given year
     *
     * @param   int year default -1 Year, defaults to this year
     * @return  util.Date date for Easter date
     * @see     http://www.koenigsmuenster.de/rsk/epakte.htm
     * @see     http://www.salesianer.de/util/kalfaq.html
     * @see     php://easter-date#user_contrib
     */
    public static function easter($year= -1) {
      if (-1 == $year) $year= date('Y');
      
      $g = $year % 19;
      $c = (int)($year / 100);
      $h = (int)($c - ($c / 4) - ((8*  $c + 13) / 25) + 19 * $g + 15) % 30;
      $i = (int)$h - (int)($h / 28) * (1 - (int)($h / 28)* (int)(29 / ($h+ 1)) * ((int)(21 - $g) / 11));
      $j = ($year + (int)($year / 4) + $i + 2 - $c + (int)($c / 4)) % 7;
      $l = $i - $j;
      $m = 3 + (int)(($l + 40) / 44);
      $d = $l + 28 - 31 * ((int)($m / 4));

      return Date::create($year, $m, $d, 0, 0, 0);
    }
    
    /**
     * Returns whether a year is a leap year
     *
     * @param   int year
     * @return  bool TRUE if the given year is a leap year
     */
    public static function isLeapYear($year) {
      return $year % 400 == 0 || ($year > 1582 && $year % 100 == 0 ? FALSE : $year % 4 == 0);
    }
  }
?>
