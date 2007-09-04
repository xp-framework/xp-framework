<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The class Date represents a specific instant in time.
   *
   * @test     xp://net.xp_framework.unittest.util.DateTest
   * @purpose  Represent a date
   */
  class Date extends Object {
    protected
      $date     = NULL;
    
    const
      DEFAULT_FORMAT    = 'Y-m-d H:i:sO',
      SERIALIZE_FORMAT  = 'Y-m-d H:i:sO';

    /**
     * Constructor. Creates a new date object through either a
     * <ul>
     *   <li>integer - interpreted as timestamp</li>
     *   <li>string - parsed into a date</li>
     *   <li>php.DateTime object - will be used as is</li>
     *   <li>NULL - creates a date representing the current instance</li>
     *  </ul>
     *
     * Timezone assignment works through these rules:
     * . If the time is given as string and contains a parseable timezone identifier
     *   that one is used.
     * . If no timezone could be determined, the timezone given by the
     *   second parameter is used
     * . If no timezone has been given as second parameter, the system's default
     *   timezone is used.
     *
     * @param   mixed in default NULL either a string or a Unix timestamp or DateTime object, defaulting to now
     * @param   string timezone default NULL string of timezone
     * @throws  lang.IllegalArgumentException in case the date is unparseable
     */
    public function __construct($in= NULL, TimeZone $timezone= NULL) {
      switch (TRUE) {
        case $in instanceof DateTime: {
          $this->date= $in;
          return;
        }
        
        // Specially mark timestamps for parsing (we assume here that strings
        // containing only digits are timestamps)
        case is_numeric($in): $in= '@'.$in;
          $timezone= NULL;
          // Break missing intentionally

        default: {
          switch (TRUE) {
            case $timezone instanceof TimeZone: {
              $this->date= date_create($in, $timezone->getHandle());
              break;
            }
            
            default: {
              $this->date= date_create($in); break;
            }
          }
          
          if (FALSE === $this->date)
            throw new IllegalArgumentException(
              'Given argument is neither a timestamp nor a well-formed timestring: "'.$in.'"'
            );
          return;
        }
      }
    }
    
    /**
     * Retrieve handle of underlying DateTime object.
     *
     * @return  php.DateTime
     */
    public function getHandle() {
      return clone $this->date;
    }
    
    /**
     * Sleep method.
     *
     * @return  array
     */
    public function __sleep() {
      $this->value= $this->toString(self::SERIALIZE_FORMAT, new TimeZone('GMT'));
      return array('value', '__id');
    }
    
    /**
     * Wakup method - reconstructs object after deserialization.
     *
     */
    public function __wakeup() {
      
      // First check for new serialization format
      if (isset($this->value)) {
        $this->date= date_create($this->value);
        return;
      }

      // Check for legacy serialization format
      if (isset($this->_utime)) {
        $this->date= date_create('@'.$this->_utime);
        unset($this->_utime, $this->seconds, $this->minutes, $this->hours, $this->mday,
          $this->wday, $this->mon, $this->year, $this->yday, $this->weekday, $this->month
        );
        return;
      }
    }
    
    /**
     * Construct a date object out of it's time values If a timezone string
     * the date will be set into that zone - defaulting to the system's
     * default timezone of none is given.
     *
     * @param   int year
     * @param   int month
     * @param   int day
     * @param   int hour
     * @param   int minute
     * @param   int second
     * @param   string tz default NULL
     * @return  util.Date
     */
    public static function create($year, $month, $day, $hour, $minute, $second, TimeZone $tz= NULL) {
      $date= date_create();
      if ($tz) {
        date_timezone_set($date, $tz->getHandle());
      }
      date_date_set($date, $year, $month, $day);
      date_time_set($date, $hour, $minute, $second);
      
      return new self($date);
    }
    
    /**
     * Create a timestamp for a date given by it's values.
     *
     * @param   int year
     * @param   int month
     * @param   int day
     * @param   int hour
     * @param   int minute
     * @param   int second
     * @param   string tz default NULL
     * @return  util.Date
     */
    #[@deprecated]
    public static function mktime($hour, $minute, $second, $month, $day, $year, TimeZone $tz= NULL) {
      return self::create($year, $month, $day, $hour, $minute, $second, $tz)->getTime();
    }
    
    /**
     * Indicates whether the date to compare equals this date.
     *
     * @param   util.Date cmp
     * @return  bool TRUE if dates are equal
     */
    public function equals($cmp) {
      return ($cmp instanceof self) && ($this->getTime() === $cmp->getTime());
    }
    
    /**
     * Static method to get current date/time
     *
     * @return  util.Date
     */
    public static function now() {
      return new self(NULL);
    }
    
    /**
     * Create a date from a string
     *
     * <code>
     *   $d= Date::fromString('2003-02-01');
     * </code>
     *
     * @see     php://date_create
     * @param   string str
     * @return  util.Date
     */
    #[@deprecated]
    public static function fromString($str, $tz= NULL) {
      return new self($str, $tz);
    }
    
    /**
     * Compare this date to another date
     *
     * @param   util.Date date A date object
     * @return  int equal: 0, date before $this: < 0, date after $this: > 0
     */
    public function compareTo(Date $date) {
      return $date->getTime()- $this->getTime();
    }
    
    /**
     * Checks whether this date is before a given date
     *
     * @param   util.Date date
     * @return  bool
     */
    public function isBefore(Date $date) {
      return $this->getTime() < $date->getTime();
    }

    /**
     * Checks whether this date is after a given date
     *
     * @param   util.Date date
     * @return  bool
     */
    public function isAfter(Date $date) {
      return $this->getTime() > $date->getTime();
    }
    
    /**
     * Retrieve Unix-Timestamp for this date
     *
     * @return  int Unix-Timestamp
     */
    public function getTime() {
      return (int)$this->date->format('U');
    }

    /**
     * Get seconds
     *
     * @return  int
     */
    public function getSeconds() {
      return (int)$this->date->format('s');
    }

    /**
     * Get minutes
     *
     * @return  int
     */
    public function getMinutes() {
      return (int)$this->date->format('i');
    }

    /**
     * Get hours
     *
     * @return  int
     */
    public function getHours() {
      return (int)$this->date->format('G');
    }

    /**
     * Get day
     *
     * @return  int
     */
    public function getDay() {
      return (int)$this->date->format('d');
    }

    /**
     * Get month
     *
     * @return  int
     */
    public function getMonth() {
      return (int)$this->date->format('m');
    }

    /**
     * Get year
     *
     * @return  int
     */
    public function getYear() {
      return (int)$this->date->format('Y');
    }

    /**
     * Get day of year
     *
     * @return  int
     */
    public function getDayOfYear() {
      return (int)$this->date->format('z');
    }

    /**
     * Get day of week
     *
     * @return  int
     */
    public function getDayOfWeek() {
      return (int)$this->date->format('w');
    }
    
    /**
     * Get name of timezone
     *
     * @return  string
     */
    public function getTimezoneName() {
      return $this->date->format('e');
    }
    
    /**
     * Retrieve timezone object associated with this date
     *
     * @return  util.TimeZone
     */
    public function getTimeZone() {
      return new TimeZone(date_timezone_get($this->date));
    }    
    
    /**
     * Create a string representation
     *
     * @see     php://date
     * @param   string format default Date::DEFAULT_FORMAT format-string
     * @return  string the formatted date
     */
    public function toString($format= self::DEFAULT_FORMAT, TimeZone $outtz= NULL) {
      if (NULL === $outtz) return date_format($this->date, $format);

      $origtz= date_timezone_get($this->date);
      date_timezone_set($this->date, $outtz->getHandle());
      $formatted= date_format($this->date, $format);
      date_timezone_set($this->date, $origtz);
      
      return $formatted;
    }
    
    /**
     * Format a date by the given strftime()-like format string
     *
     * @see     php://strftime
     * @param   string format
     * @return  string
     * @throws  lang.IllegalArgumentException if unsupported token has been given
     */
    public function format($format) {
      return preg_replace_callback('#%([a-zA-Z])#', array($this, 'formatCallback'), $format);
    }
    
    /**
     * Format callback function. Do not use directly
     *
     * @param   string[] matches
     * @return  string
     * @throws  lang.IllegalArgumentException if unsupported token has been given
     */
    public function formatCallback($matches) {
      static $map= array(
        'd' => 'd',
        'm' => 'm',
        'Y' => 'Y',
        'H' => 'H',
        'i' => 'i',
        'S' => 's',
        'w' => 'w',
        'G' => 'o',
        'D' => 'm/d/Y',
        'T' => 'H:i:s',
        'Z' => 'e',
        'G' => 'o',
        'V' => 'W'
      );
      static $rep= array(
        't' => "\t",
        'n' => "\n",
      );
      
      if (isset($map[$matches[1]])) return $this->toString($map[$matches[1]]);
      if (isset($rep[$matches[1]])) return $rep[$matches[1]];
      
      // Other tokens that are actually supported by strftime() have been
      // left out intentionally, because either they are
      // a) hard to implement and never / seldom used in the framework
      // b) locale-dependent, this should not be supported in any
      //    way by the framework.
      throw new IllegalArgumentException('Illegal date format token: "'.$matches[1].'"');
    }
  }
?>
