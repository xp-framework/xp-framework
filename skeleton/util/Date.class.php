<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The class Date represents a specific instant in time.
   *
   * Public member variables:
   * <pre>
   * seconds    - Seconds
   * minutes    - Minutes
   * hours      - Hours (0 .. 24)
   * mday       - Day of the month
   * wday       - Day of the week (0 = Sunday, .. , 6 = Saturday)
   * mon        - Month
   * year       - Year
   * yday       - Day of the year
   * weekday    - Day of the week name, long, e.g. "Friday"
   * month      - Month name, long, e.g. "January"
   * </pre>
   *
   * @purpose  Represent a date
   */
  class Date extends Object {
    var
      $_utime   = 0;
      
    var
      $seconds  = 0,
      $minutes  = 0,
      $hours    = 0,
      $mday     = 0,
      $wday     = 0,
      $mon      = 0,
      $year     = 0,
      $yday     = 0,
      $weekday  = '',
      $month    = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   mixed in default NULL either a string or a Unix timestamp, defaulting to now
     */
    function __construct($in= NULL) {
      if (is_string($in)) {
        $this->_utime(strtotime($in));
      } elseif (is_int($in)) {
        $this->_utime($in);
      } else {
        $this->_utime(time());
      }
      parent::__construct();
    }
    
    /**
     * Static method to get current date/time
     *
     * @model   static
     * @access  public
     * @return  &util.Date
     */
    function &now() {
      return new Date(NULL);
    }
    
    /**
     * Create a date from a string
     *
     * <code>
     *   $d= &Date::fromString('yesterday');
     *   $d= &Date::fromString('2003-02-01');
     * </code>
     *
     * @access  public
     * @model   static
     * @see     php://strtotime
     * @param   string str
     * @return  &util.Date
     */
    function &fromString($str) {
      return new Date(strtotime($str));
    }
    
    /**
     * Private helper function which sets all of the public member variables
     *
     * @access  private
     * @param   int utime Unix-Timestamp
     */
    function _utime($utime) {
      foreach (getdate($this->_utime= $utime) as $key => $val) {
        if (is_string($key)) $this->$key= $val;
      }
    }
    
    /**
     * Compare this date to another date
     *
     * @access  public
     * @param   &util.Date date A date object
     * @return  int equal: 0, date before $this: < 0, date after $this: > 0
     */
    function compareTo(&$date) {
      return $date->getTime()- $this->getTime();
    }
    
    /**
     * Checks whether this date is before a given date
     *
     * @access  public
     * @param   &util.Date date
     * @return  bool
     */
    function isBefore(&$date) {
      return $this->getTime() < $date->getTime();
    }

    /**
     * Checks whether this date is after a given date
     *
     * @access  public
     * @param   &util.Date date
     * @return  bool
     */
    function isAfter(&$date) {
      return $this->getTime() > $date->getTime();
    }
    
    /**
     * Retrieve Unix-Timestamp for this date
     *
     * @access  public
     * @return  int Unix-Timestamp
     */
    function getTime() {
      return $this->_utime;
    }

    /**
     * Get seconds
     *
     * @access  public
     * @return  int
     */
    function getSeconds() {
      return $this->seconds;
    }

    /**
     * Get minutes
     *
     * @access  public
     * @return  int
     */
    function getMinutes() {
      return $this->minutes;
    }

    /**
     * Get hours
     *
     * @access  public
     * @return  int
     */
    function getHours() {
      return $this->hours;
    }

    /**
     * Get day
     *
     * @access  public
     * @return  int
     */
    function getDay() {
      return $this->mday;
    }

    /**
     * Get month
     *
     * @access  public
     * @return  int
     */
    function getMonth() {
      return $this->mon;
    }

    /**
     * Get year
     *
     * @access  public
     * @return  int
     */
    function getYear() {
      return $this->year;
    }

    /**
     * Get day of year
     *
     * @access  public
     * @return  int
     */
    function getDayOfYear() {
      return $this->yday;
    }

    /**
     * Get day of week
     *
     * @access  public
     * @return  int
     */
    function getDayOfWeek() {
      return $this->wday;
    }
    
    /**
     * Create a string representation
     *
     * @access  public
     * @see     php://date
     * @param   string format default 'r' format-string
     * @return  string the formatted date
     */
    function toString($format= 'r') {
      return date($format, $this->_utime);
    }

    /**
     * Format date
     *
     * @access  public
     * @see     php://strftime
     * @param   string format default '%c' format-string
     * @return  string the formatted date
     */
    function format($format= '%c') {
      return strftime($format, $this->_utime);
    }
  }
?>
