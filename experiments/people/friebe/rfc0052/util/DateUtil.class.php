/* This class is part of the XP framework
 *
 * $Id$ <?php
 */

package util {
  import util~Date;
  import util~TimeSpan;
  
  /**
   * DateUtil is a helper class to handle Date objects and 
   * calculate date- and timestamps.
   *
   * @test    xp://util.DateUtil
   * @purpose Utils to calculate with Date objects
   */
  class DateUtil extends lang~Object {

    /**
     * Returns a Date object which represents the date at
     * the given date at midnight.
     *
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @return  &util.Date
     */
    public function &getMidnight(&$date) {
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
    public function &getLastOfMonth(&$date) {
      return new Date(Date::mktime (
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
    public function &getFirstOfMonth(&$date) {
      return new Date(Date::mktime (
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
    public function &addMonths(&$date, $count= 1) {
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
     * @model   static
     * @access  public
     * @param   &util.Date date
     * @param   int count default 1
     * @return  &util.Date
     */
    public function &addWeeks(&$date, $count= 1) {
      return self::addDays($date, $count * 7);
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
    public function &addDays(&$date, $count= 1) {
      return new Date(Date::mktime(
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
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    public function &addHours(&$date, $count= 1) {
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
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    public function &addMinutes(&$date, $count= 1) {
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
     * @model   static
     * @access  public
     * @param   &util.Date date 
     * @param   int count default 1
     * @return  &util.Date
     */
    public function &addSeconds(&$date, $count= 1) {
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
     * @model   static
     * @access  public
     * @param   &util.Date d1
     * @param   &util.Date d2
     * @return  &util.TimeSpan
     */
    public function &timeSpanBetween(&$d1, &$d2) {
      return new TimeSpan($d1->getTime()-$d2->getTime());  
    }

  }
}
