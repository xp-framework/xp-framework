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
    protected
      $_utime   = 0;
      
    public
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
    public function __construct($in= NULL) {
      if (is_string($in) && (-1 !== ($time= strtotime($in)))) {
        self::_utime($time);
      } elseif (is_int($in)) {
        self::_utime($in);
      } elseif (is_null($in)) {
        self::_utime(time());
      } else {
        self::_utime(time());
        throw (new IllegalArgumentException(
          'Given argument is neither a timestamp nor a well-formed timestring'
        ));
      }
      
      
    }
    
    /**
     * Static method to get current date/time
     *
     * @model   static
     * @access  public
     * @return  &util.Date
     */
    public static function now() {
      return new Date(NULL);
    }
    
    /**
     * Create a date from a string
     *
     * <code>
     *   $d= Date::fromString('yesterday');
     *   $d= Date::fromString('2003-02-01');
     * </code>
     *
     * @access  public
     * @model   static
     * @see     php://strtotime
     * @param   string str
     * @return  &util.Date
     */
    public static function fromString($str) {
      return new Date($str);
    }
    
    /**
     * Private helper function which sets all of the public member variables
     *
     * @access  private
     * @param   int utime Unix-Timestamp
     */
    private function _utime($utime) {
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
    public function compareTo(Date $date) {
      return $date->getTime()- self::getTime();
    }
    
    /**
     * Checks whether this date is before a given date
     *
     * @access  public
     * @param   &util.Date date
     * @return  bool
     */
    public function isBefore(Date $date) {
      return self::getTime() < $date->getTime();
    }

    /**
     * Checks whether this date is after a given date
     *
     * @access  public
     * @param   &util.Date date
     * @return  bool
     */
    public function isAfter(Date $date) {
      return self::getTime() > $date->getTime();
    }
    
    /**
     * Retrieve Unix-Timestamp for this date
     *
     * @access  public
     * @return  int Unix-Timestamp
     */
    public function getTime() {
      return $this->_utime;
    }

    /**
     * Get seconds
     *
     * @access  public
     * @return  int
     */
    public function getSeconds() {
      return $this->seconds;
    }

    /**
     * Get minutes
     *
     * @access  public
     * @return  int
     */
    public function getMinutes() {
      return $this->minutes;
    }

    /**
     * Get hours
     *
     * @access  public
     * @return  int
     */
    public function getHours() {
      return $this->hours;
    }

    /**
     * Get day
     *
     * @access  public
     * @return  int
     */
    public function getDay() {
      return $this->mday;
    }

    /**
     * Get month
     *
     * @access  public
     * @return  int
     */
    public function getMonth() {
      return $this->mon;
    }

    /**
     * Get year
     *
     * @access  public
     * @return  int
     */
    public function getYear() {
      return $this->year;
    }

    /**
     * Get day of year
     *
     * @access  public
     * @return  int
     */
    public function getDayOfYear() {
      return $this->yday;
    }

    /**
     * Get day of week
     *
     * @access  public
     * @return  int
     */
    public function getDayOfWeek() {
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
    public function toString($format= 'r') {
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
    public function format($format= '%c') {
      return strftime($format, $this->_utime);
    }
  }
?>
