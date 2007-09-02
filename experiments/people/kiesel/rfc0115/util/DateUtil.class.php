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
   * @test    xp://net.xp_framework.unittest.util.DateUtilTest
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
    public static function getMidnight(Date $date) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        0,
        0,
        0,
        $date->getTimeZone()
      );
    }
    
    /**
     * Gets the last day of the month
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getLastOfMonth(Date $date) {
      return Date::create(
        $date->getYear(),
        $date->getMonth() + 1,
        0,
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getTimeZone()
      );
    }
    
    /**
     * Gets the first day of the month
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getFirstOfMonth(Date $date) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        1,
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getTimeZone()
      );
    }

    /**
     * Gets the first day of the week
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getBeginningOfWeek(Date $date) {
      return DateUtil::addDays(DateUtil::getMidnight($date), -$date->getDayOfWeek());
    }

    /**
     * Gets the last day of the week
     *
     * @param   util.Date date
     * @return  util.Date
     */
    public static function getEndOfWeek(Date $date) {
      $date= Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        23,
        59,
        59,
        $date->getTimeZone()
      );
      return DateUtil::addDays($date, 6- $date->getDayOfWeek());
    }

    /**
     * Adds a positive or negative amount of months
     *
     * @param   util.Date date
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addMonths(Date $date, $count= 1) {
      return Date::create(
        $date->getYear(),
        $date->getMonth() + $count,
        $date->getDay(),
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getTimeZone()
      );
    }

    /**
     * Adds a positive or negative amount of weeks
     *
     * @param   util.Date date
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addWeeks(Date $date, $count= 1) {
      return DateUtil::addDays($date, $count * 7);
    }
    
    /**
     * Adds a positive or negative amount of days
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addDays(Date $date, $count= 1) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay() + $count,
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getTimeZone()
      );
    }
    
    /**
     * Adds a positive or negative amount of hours
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addHours(Date $date, $count= 1) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        $date->getHours() + $count,
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getTimeZone()
      );
    }
    
    /**
     * Adds a positive or negative amount of minutes
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addMinutes(Date $date, $count= 1) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        $date->getHours(),
        $date->getMinutes() + $count,
        $date->getSeconds(),
        $date->getTimeZone()
      );
    }

    /**
     * Adds a positive or negative amount of seconds
     *
     * @param   util.Date date 
     * @param   int count default 1
     * @return  util.Date
     */
    public static function addSeconds(Date $date, $count= 1) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds() + $count,
        $date->getTimeZone()
      );
    }
    
    /**
     * Move a date to a given timezone. Does not modify the date's
     * actual value.
     *
     * @param   util.Date date
     * @param   util.TimeZone tz
     * @return  util.Date
     */
    public static function moveToTimezone(Date $date, TimeZone $tz) {
      return $tz->translate($date);
    }
    
    /**
     * Set a given timezone for the passed date. Really modifies
     * the date as just the timezone is exchanged, no further
     * modifications are done.
     *
     * @param   util.Date date
     * @param   util.TimeZone tz
     * @return  util.Date
     */
    public static function setTimezone(Date $date, TimeZone $tz) {
      return Date::create(
        $date->getYear(),
        $date->getMonth(),
        $date->getDay(),
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $tz
      );
    }    

    /**
     * Returns a TimeSpan representing the difference 
     * between the two given Date objects
     *
     * @param   util.Date d1
     * @param   util.Date d2
     * @return  util.TimeSpan
     */
    public static function timeSpanBetween(Date $d1, Date $d2) {
      return new TimeSpan($d1->getTime()- $d2->getTime());
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
    public static function compare(Date $a, Date $b) {
      return $b->compareTo($a);
    }
  } 
?>
