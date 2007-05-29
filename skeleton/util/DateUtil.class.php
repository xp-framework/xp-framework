<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Date',
    'util.TimeSpan'
  );
  
  /**
   * DateUtil is a helper class to handle Date objects and 
   * calculate date- and timestamps.
   *
   * @test    xp://util.DateUtil
   * @purpose Utils to calculate with Date objects
   */
  class DateUtil extends Object {

    /**
     * Returns a Date object which represents the date at
     * the given date at midnight.
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getMidnight($date) {
      $d= new Date(Date::mktime(
        0,
        0,
        0,
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
      return $d;
    }
    
    /**
     * Gets the last day of the month
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getLastOfMonth($date) {
      $d= new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth() + 1,
        0,
        $date->getYear()
      ));
      return $d;
    }
    
    /**
     * Gets the first day of the month
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getFirstOfMonth($date) {
      $d= new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        1,
        $date->getYear()
      ));
      return $d;
    }

    /**
     * Gets the first day of the week
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getBeginningOfWeek($date) {
      return DateUtil::addDays(DateUtil::getMidnight($date), -$date->getDayOfWeek());
    }

    /**
     * Gets the last day of the week
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getEndOfWeek($date) {
      $date= new Date(Date::mktime(
        23,
        59,
        59,
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
      return DateUtil::addDays($date, 6 - $date->wday);
    }

    /**
     * Adds a positive or negative amount of months
     *
     * @param   util.Date date
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addMonths($date, $count= 1) {
      return new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth() + $count,
        $date->getDay(),
        $date->getYear()
      ));
    }

    /**
     * Adds a positive or negative amount of weeks
     *
     * @param   util.Date date
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addWeeks($date, $count= 1) {
      return DateUtil::addDays($date, $count * 7);
    }
    
    /**
     * Adds a positive or negative amount of days
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addDays($date, $count= 1) {
      return  new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay() + $count,
        $date->getYear()
      ));
    }
    
    /**
     * Adds a positive or negative amount of hours
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addHours($date, $count= 1) {
      return new Date(Date::mktime(
        $date->getHours() + $count,
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
    }
    
    /**
     * Adds a positive or negative amount of minutes
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addMinutes($date, $count= 1) {
      return new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes() + $count,
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
    }

    /**
     * Adds a positive or negative amount of seconds
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addSeconds($date, $count= 1) {
      return new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds() + $count,
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
    }

    /**
     * returns a TimeSpan representing the difference 
     * between the two given Date objects
     *
     * @param   util.Date d1
     * @param   util.Date d2
     * @return  util.TimeSpan
     */
    public static function timeSpanBetween($d1, $d2) {
      return new TimeSpan($d1->getTime()-$d2->getTime());
    }

    /**
     * Comparator method for two Date objects
     *
     * Returns a negative number if a < b, a positive number if a > b 
     * and 0 if both dates are equal
     *
     * Example usage with usort():
     * <code>
     *   usort($datelist, array('DateUtil', 'compare'))
     * </code>
     *
     * @param   util.Date a
     * @param   util.Date b
     * @return  int
     */
    public static function compare($a, $b) {
      return $b->compareTo($a);
    }

  } 
?>
