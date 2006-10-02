<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses ('util.Date', 'util.TimeSpan');
  
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
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @return  &util.Date
     */
    function &getMidnight(&$date) {
      $d= &new Date(Date::mktime (
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
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @return  &util.Date
     */
    function &getLastOfMonth(&$date) {
      $d= &new Date(Date::mktime (
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
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @return  &util.Date
     */
    function &getFirstOfMonth(&$date) {
      $d= &new Date(Date::mktime (
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
     * Adds a positive or negative amount of months
     *
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addMonths(&$date, $count= 1) {
      $d= &new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth() + $count,
        $date->getDay(),
        $date->getYear()
      ));
      return $d;
    }

    /**
     * Adds a positive or negative amount of weeks
     *
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addWeeks(&$date, $count= 1) {
      return DateUtil::addDays($date, $count * 7);
    }
    
    /**
     * Adds a positive or negative amount of days
     *
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addDays(&$date, $count= 1) {
      $d= &new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay() + $count,
        $date->getYear()
      ));
      return $d;
    }
    
    /**
     * Adds a positive or negative amount of hours
     *
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addHours(&$date, $count= 1) {
      $d= &new Date(Date::mktime(
        $date->getHours() + $count,
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
      return $d;
    }
    
    /**
     * Adds a positive or negative amount of minutes
     *
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addMinutes(&$date, $count= 1) {
      $d= &new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes() + $count,
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
      return $d;
    }

    /**
     * Adds a positive or negative amount of seconds
     *
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addSeconds(&$date, $count= 1) {
      $d= &new Date(Date::mktime(
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds() + $count,
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
      return $d;
    }

    /**
     * returns a TimeSpan representing the difference 
     * between the two given Date objects
     *
     * @model   static
     * @access  public
     * @param   &util.Date d1
     * @param   &util.Date d2
     * @return  &util.TimeSpan
     */
    function &timeSpanBetween(&$d1, &$d2) {
      $t= &new TimeSpan($d1->getTime()-$d2->getTime());
      return $t;
    }

    /**
     * Comparator method for two Date objects
     * <br/>
     * Returns -1 if a < b, 1 if a > b and 0 if both dates are equal
     *
     * @model   static
     * @access  public
     * @param   &util.Date a
     * @param   &util.Date b
     * @return  int
     */
    function compare(&$a, &$b) {
      if ($a->isBefore($b)) return -1;
      if ($a->isAfter($b)) return 1;
      if ($a->equals($b)) return 0;
      return 0;
    }

  }
?>
