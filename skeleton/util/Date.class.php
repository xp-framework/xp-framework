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
      $seconds,	
      $minutes,	
      $hours,	
      $mday,	
      $wday,	
      $mon,	
      $year,	
      $yday,	
      $weekday,	
      $month;	

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
     * Create a date from a string
     *
     * <code>
     *   $d= &Date::fromString('yesterday');
     *   $d= &Date::fromString('2003-02-01');
     * </code>
     *
     * @access  public
     * @model   static
     * @see     http://php.net/strtotime
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
      $a= getdate($this->_utime= $utime);
      foreach ($a as $key=> $val) {
        if (is_string($key)) $this->$key= $val;
      }
    }
    
    /**
     * Compare this date to another date
     *
     * @access  public
     * @param   &util.Date date A date object
     * @return  int equal: 0, date before $this: <0, date after $this: >0
     */
    function compareTo(&$date) {
      return strcmp(
        (string)$date->_utime, 
        (string)$this->_utime
      );
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
     * Create a string representation
     *
     * @see     php://date
     * @param   string format default 'r' format-string
     * @return  string the formatted date
     */
    function toString($format= 'r') {
      return date($format, $this->_utime);
    }
  }
?>
