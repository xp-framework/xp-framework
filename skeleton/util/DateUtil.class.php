<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses ('util.Date');
  
  /**
   * DateUtil is a helper class to handle Date objects and 
   * calculate date- and timestamps.
   *
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
      return new Date(Date::mktime (
        0,
        0,
        0,
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
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
      return new Date (mktime (
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth() + 1,
        0,
        $date->getYear()
      ));
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
      return new Date (mktime (
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        1,
        $date->getYear()
      ));
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
      return new Date (mktime (
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
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @param   int count default 1
     * @return  &util.Date
     */
    function &addWeeks(&$date, $count= 1) {
      return new Date($date->getTime() + $count * 604800);
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
      return new Date($date->getTime() + $count * 86400);
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
      return new Date($date->getTime() + $count * 3600);
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
      return new Date($date->getTime() + $count * 60);
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
      return new Date($date->getTime() + $count);
    }
  }
?>
