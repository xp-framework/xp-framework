<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.Date');

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
     * @param   &util.Date
     * @return  &util.Date
     */
    public static function getMidnight(&$date) {
      return new Date (mktime (
        23,
        59,
        00,
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
     * @param   &util.Date
     * @return  &util.Date
     */
    public static function getLastOfMonth(&$date) {
      return new Date (mktime (
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth()+1,
        0,
        $date->getYear()
      ));
    }
    
    /**
     * Gets the first day of the month
     *
     * @model   static
     * @access  public
     * @param   &util.Date
     * @return  &util.Date
     */
    public static function getFirstOfMonth(&$date) {
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
     * @param   &util.Date
     * @param   int count
     * @return  &util.Date
     */
    public static function addMonths(&$date, $cnt= 1) {
      return new Date (mktime (
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth() + $cnt,
        $date->getDay(),
        $date->getYear()
      ));
    }
    
    /**
     * Adds a positive or negative amount of days
     *
     * @model   static
     * @access  public
     * @param   &util.Date
     * @param   int count
     * @return  &util.Date
     */
    public static function addDays(&$date, $cnt= 1) {
      return new Date (mktime (
        $date->getHours(),
        $date->getMinutes(),
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay() + $cnt,
        $date->getYear()
      ));
    }
    
    /**
     * Adds a positive or negative amount of hours
     *
     * @model   static
     * @access  public
     * @param   &util.Date
     * @param   int count
     * @return  &util.Date
     */
    public static function addHours(&$date, $cnt= 1) {
      return new Date (mktime (
        $date->getHours() + $cnt,
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
     * @model   static
     * @access  public
     * @param   &util.Date
     * @param   int count
     * @return  &util.Date
     */
    public static function addMinutes(&$date, $cnt= 1) {
      return new Date (mktime (
        $date->getHours(),
        $date->getMinutes() + $cnt,
        $date->getSeconds(),
        $date->getMonth(),
        $date->getDay(),
        $date->getYear()
      ));
    }
  }
?>
