<?php
/* This class is part of the XP framework
 *
 * $Id: Date.class.php 8977 2006-12-28 12:02:51Z friebe $ 
 */

  namespace util;

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
   * @test     xp://net.xp_framework.unittest.DateTest
   * @purpose  Represent a date
   */
  class Date extends lang::Object {
    public
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
     * @param   mixed in default NULL either a string or a Unix timestamp, defaulting to now
     * @throws  lang.IllegalArgumentException in case the date is unparseable
     */
    public function __construct($in= NULL) {
      if (is_string($in)) {
        $this->_utime(self::_strtotime($in));
      } else if (is_int($in) || is_float($in)) {
        $this->_utime($in);
      } else if (is_null($in)) {
        $this->_utime(time());
      } else {
        $this->_utime(time());
        throw(new lang::IllegalArgumentException(
          'Given argument is neither a timestamp nor a well-formed timestring'
        ));
      }
    }
    
    /**
     * Get local time zones' offset from GMT (Greenwich main time). 
     * Caches the result.
     *
     * @return  int offset in seconds
     */
    protected function _getGMTOffset() {
      static $o;
      
      if (!isset($o)) $o= mktime(0, 0, 0, 1, 2, 1970, 0)- gmmktime(0, 0, 0, 1, 2, 1970, 0);
      return $o;
    }
    
    /**
     * Returns whether a year is a leap year
     *
     * @param   int year
     * @return  bool TRUE if the given year is a leap year
     */
    protected static function _isLeapYear($year) {
      return $year % 400 == 0 || ($year > 1582 && $year % 100 == 0 ? FALSE : $year % 4 == 0);
    }
    
    /**
     * Overflow-safe replacement for PHP's strtotime() function.
     *
     * @param   string in
     * @return  int
     */
    protected static function _strtotime($in) {
      static $month_names= array(
        'Jan' => 1,
        'Feb' => 2,
        'Mar' => 3,
        'Apr' => 4,
        'May' => 5,
        'Jun' => 6,
        'Jul' => 7,
        'Aug' => 8,
        'Sep' => 9,
        'Oct' => 10,
        'Nov' => 11,
        'Dec' => 12
      );
      
      // Try to use builtin function strtotime()
      if (-1 != ($stamp= strtotime($in)) && FALSE !== $stamp) return $stamp;
      
      // European date format (dd.mm.yyyy hh:mm:ss). At least two values
      // need to be found
      if (2 < sscanf($in, '%d.%d.%d %d:%d:%d', $d, $m, $y, $h, $i, $s)) {
        return self::mktime($h, $i, $s, $m, $d, $y);
      }

      // "2006-05-04 11:59:00"
      if (2 < sscanf($in, '%4d-%02d-%02d %02d:%02d:%02d', $y, $m, $d, $h, $i, $s)) {
        return self::mktime($h, $i, $s, $m, $d, $y);
      }
      
      // "Dec 31 2070 11:59PM"
      if (2 < sscanf($in, '%3s %02d %04d %02d:%02d%[AP]M', $n, $d, $y, $h, $i, $m)) {
        ($m == 'A' && $h == 12) && $h= 0;
        ($m == 'A') || ($m == 'P' && $h == 12) || $h+= 12;
        return self::mktime($h, $i, 0, $month_names[$n], $d, $y);
      }
      
      // FIXME: Support more formats
      
      throw(new lang::IllegalArgumentException('Cannot parse "'.$in.'"'));
    }
    
    /**
     * Overflow-safe replacement for PHP's mktime() function. Uses the builtin
     * function in case the year is between 1971 and 2037.
     *
     * @see     php://mktime
     * @param   int hour default 0
     * @param   int minute default 0
     * @param   int second default 0
     * @param   int month default 0
     * @param   int day default 0
     * @param   int year default 0
     * @param   int is_dst default -1
     * @return  int stamp
     */
    public static function mktime($hour= 0, $minute= 0, $second= 0, $month= 0, $day= 0, $year= 0, $is_dst= -1) {
      static $month_table= array(
        array(NULL, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
        array(NULL, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31) // Leap years
      );
    
      // Use builtin?
      if (1971 < $year && $year < 2038) {
        return mktime($hour, $minute, $second, $month, $day, $year, $is_dst);
      }
      
      $gmt= 0;  // FIXME

      // Check for month overflow and advance into next year
      if ($month > 12) {
        $y= floor($month / 12);
        $year+= $y;
        $month-= $y * 12;
      }

      $days= 0;
      if ($year >= 1970) {

        // Add number of years times number of days per year to days
        for ($y= 1970; $y < $year; $y++) {
          $days+= self::_isLeapYear($y) ? 366 : 365;
        }
        
        // Add number of days per month
        $days+= array_sum(array_slice($month_table[self::_isLeapYear($year)], 1, $month- 1));
        
        // Add day
        $days+= $day- 1;
        
        // Calculate stamp
        $stamp= $days * 86400 + $hour * 3600 + $minute * 60 + $second + $gmt;
      } else {
      
        // Add number of years times number of days per year to days
        for ($y= 1969; $y > $year; $y--) {
          $days+= self::_isLeapYear($y) ? 366 : 365;
        }
        $leap= self::_isLeapYear($year);
        
        // Add number of days per month
        $days+= array_sum(array_slice($month_table[$leap], $month + 1, 12));
        
        // Subtract day
        $days+= $month_table[$leap][intval($month)]- $day;
        
        // Calculate stamp
        $stamp= - ($days * 86400 + (86400 - ($hour * 3600 + $minute * 60 + $second)) - $gmt);
        
        // Gregorian correction
        if ($stamp < -12220185600) {
          $stamp+= 864000; 
        } else if ($stamp < -12219321600) {
          $stamp = -12219321600;
        }
      } 

      return $stamp;
    }
    
    /**
     * Overflow-safe replacement for PHP's getdate() function. Uses the
     * builtin function when 0 <= stamp <= LONG_MAX, the userland 
     * implementation otherwise.
     *
     * @see     php://getdate
     * @param   int stamp
     * @return  array
     */
    protected static function _getdate($stamp, $isGMT= FALSE) {
      static $month_table= array(
        array(NULL, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
        array(NULL, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31) // Leap years
      );
      
      // Use builtin?
      if ($stamp >= 0 && $stamp <= LONG_MAX) return getdate($stamp);
      
      $result= array(); 
      $gc= 0;
      if ($stamp < 0) {

        // Oct 15, 1582 or earlier
        if ($stamp < -12219321600) $stamp-= 864000;
      
        // Look for year
        for ($year= 1970; --$year >= 0; ) {
          $last= $stamp;
          $leap= self::_isLeapYear($year);
          $stamp+= $leap ? 31622400 : 31536000;
          if ($stamp >= 0) {
            $result['year']= $year;
            break;
          }
        }
        $seconds= 31536000 + (86400 * $leap) + $last;
        $result['leap']= $leap;

        // Look for month
        $stamp= $last;
        for ($month= 13; --$month > 0; ) {
          $last= $stamp;
          $stamp+= $month_table[$leap][$month] * 86400;
          if ($stamp >= 0) {
            $result['mon']= $month;
            $result['ndays']= $month_table[$leap][$month];
            break;
          }
        }

        // Figure out day
        $stamp= $last;
        $result['mday']= $result['ndays']+ ceil(($stamp+ 1) / 86400);

        // Figure out hour
        $stamp+= ($result['ndays']- $result['mday']+ 1) * 86400;
        $result['hours']= floor($stamp / 3600);
        
        // Gregorian correction value
        $gc= ($result['year'] < 1582 || ($result['year'] == 1582 && $result['mon'] == 10 && $result['mday'] < 15)) ? 3 : 0;
      } else {

        // Look for year
        for ($year= 1970; ; $year++) {
          $last= $stamp;

          $leap= self::_isLeapYear($year);
          if (0 >= ($stamp-= $leap ? 31622400 : 31536000)) {
            $result['year']= $year;
            break;
          }
        }
        $seconds= $last;
        $result['leap']= $leap;
        
        // Look for month
        $stamp= $last;
        for ($month= 1; $month <= 12; $month++) {
          $last= $stamp;
          if (0 >= ($stamp-= $month_table[$leap][$month] * 86400)) {
            $result['mon']= $month;
            $result['ndays']= $month_table[$leap][$month];
            break;
          }
        }

        // Figure out day
        $stamp= $last;
        $result['mday']= ceil(($stamp+ 1) / 86400);
        
        // Figure out hour
        $stamp-= ($result['mday']- 1) * 86400;
        $result['hours']= floor($stamp / 3600);
      }
      
      // Figure out minutes and seconds
      $stamp-= $result['hours'] * 3600;
      $result['minutes']= floor($stamp / 60);
      $result['seconds']= $stamp - $result['minutes'] * 60;
      
      // Figure out day of year
      $result['yday']= floor($seconds / 86400);
      
      // Figure out day of week
      if ($month > 2) $month-= 2; else {
        $year--;
        $month+= 10;
      }
      $d= (
        floor((13 * $month - 1) / 5) + 
        $result['mday'] + ($year % 100) +
        floor(($year % 100) / 4) +
        floor(($year / 100) / 4) - 2 *
        floor($year / 100) + 77
      );
      $result['wday']= (($d - 7 * floor($d / 7))) + $gc;
      $result['weekday']= gmdate('l', 86400 * (3 + $result['wday']));
      $result['month']= gmdate('F', mktime(0, 0, 0, $result['mon'], 2, 1971));
      return $result;
    }
    
    /**
     * Indicates whether the date to compare equals this date.
     *
     * @param   &util.Date cmp
     * @return  bool TRUE if dates are equal
     */
    public function equals($cmp) {
      return is('util.Date', $cmp) && ($this->getTime() === $cmp->getTime());
    }    
    
    /**
     * Static method to get current date/time
     *
     * @return  &util.Date
     */
    public static function now() {
      return new self(NULL);
    }
    
    /**
     * Create a date from a string
     *
     * <code>
     *   $d= Date::fromString('yesterday');
     *   $d= Date::fromString('2003-02-01');
     * </code>
     *
     * @see     php://strtotime
     * @param   string str
     * @return  &util.Date
     */
    public static function fromString($str) {
      return new self($str);
    }
    
    /**
     * Private helper function which sets all of the public member variables
     *
     * @param   int utime Unix-Timestamp
     */
    protected function _utime($utime) {
      foreach ($this->_getdate($this->_utime= $utime) as $key => $val) {
        is_string($key) && $this->{$key}= $val;
      }
    }
    
    /**
     * Compare this date to another date
     *
     * @param   &util.Date date A date object
     * @return  int equal: 0, date before $this: < 0, date after $this: > 0
     */
    public function compareTo($date) {
      return $date->getTime()- $this->getTime();
    }
    
    /**
     * Checks whether this date is before a given date
     *
     * @param   &util.Date date
     * @return  bool
     */
    public function isBefore($date) {
      return $this->getTime() < $date->getTime();
    }

    /**
     * Checks whether this date is after a given date
     *
     * @param   &util.Date date
     * @return  bool
     */
    public function isAfter($date) {
      return $this->getTime() > $date->getTime();
    }
    
    /**
     * Retrieve Unix-Timestamp for this date
     *
     * @return  int Unix-Timestamp
     */
    public function getTime() {
      return $this->_utime;
    }

    /**
     * Get seconds
     *
     * @return  int
     */
    public function getSeconds() {
      return $this->seconds;
    }

    /**
     * Get minutes
     *
     * @return  int
     */
    public function getMinutes() {
      return $this->minutes;
    }

    /**
     * Get hours
     *
     * @return  int
     */
    public function getHours() {
      return $this->hours;
    }

    /**
     * Get day
     *
     * @return  int
     */
    public function getDay() {
      return $this->mday;
    }

    /**
     * Get month
     *
     * @return  int
     */
    public function getMonth() {
      return $this->mon;
    }

    /**
     * Get year
     *
     * @return  int
     */
    public function getYear() {
      return $this->year;
    }

    /**
     * Get day of year
     *
     * @return  int
     */
    public function getDayOfYear() {
      return $this->yday;
    }

    /**
     * Get day of week
     *
     * @return  int
     */
    public function getDayOfWeek() {
      return $this->wday;
    }
    
    /**
     * Create a string representation
     *
     * @see     php://date
     * @param   string format default 'r' format-string
     * @return  string the formatted date
     */
    public function toString($format= 'r') {
      static $daynames= array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
      static $monthnames= array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
      static $suffix= array('th', 'st', 'nd', 'rd', 'th');

      // Use builtin?
      if (1971 < $this->year && $this->year < 2038) return date($format, $this->_utime);
      
      $return= '';
      $gmt= self::_getGMTOffset();
      for ($i= 0, $s= strlen($format); $i < $s; $i++) {
        switch ($format{$i}) {
          case 'a': $return.= $this->hours > 12 ? 'pm' : 'am'; break;
          case 'A': $return.= $this->hours > 12 ? 'PM' : 'AM'; break;
          case 'B': $return.= date('B', mktime($this->hours, $this->minutes, $this->seconds)); break;
          case 'c': $return.= sprintf(
              '%04d-%02d-%02dT%02d:%02d:%02d%s%2d:%2d', 
              $this->year,
              $this->mon,
              $this->mday,
              $this->hours,
              $this->minutes,
              $this->seconds,
              $gmt < 0 ? '+' : '-',
              abs($gmt) / 36,
              abs($gmt) / 18
            );
            break;
          case 'd': $return.= sprintf('%02d', $this->mday); break;
          case 'D': $return.= $daynames[$this->wday]; break;
          case 'F': $return.= $this->month; break;
          case 'g': $return.= $this->hours == 0 ? 12 : ($this->hours > 12 ? $this->hours - 12 : $this->hours); break;
          case 'G': $return.= $this->hours; break;
          case 'h': $return.= sprintf('%02d', $this->hours == 0 ? 12 : ($this->hours > 12 ? $this->hours - 12 : $this->hours)); break;
          case 'H': $return.= sprintf('%02d', $this->hours); break;
          case 'i': $return.= sprintf('%02d', $this->minutes); break;
          case 'I': $return.= '???IS_DST???'; break;        // FIXME
          case 'j': $return.= $this->mday; break;
          case 'l': $return.= $this->weekday; break;
          case 'L': $return.= (int)$this->leap; break;
          case 'm': $return.= sprintf('%02d', $this->mon); break;
          case 'M': $return.= $monthnames[$this->mon]; break;
          case 'n': $return.= $this->mon; break;
          case 'O': $return.= sprintf('%s%04d', $gmt < 0 ? '+' : '-', abs($gmt) / 36); break;
          case 'r': $return.= sprintf(
              '%3s, %02d %3s %04s %02d:%02d:%02d %s%04d',
              $daynames[$this->wday],
              $this->mday,
              $monthnames[$this->mon],
              $this->year,
              $this->hours,
              $this->minutes,
              $this->seconds,
              $gmt < 0 ? '+' : '-',
              abs($gmt) / 36
            );
            break;
          case 's': $return.= sprintf('%02d', $this->seconds); break;
          case 'S': $return.= $suffix[max($this->mday % 10, 4)]; break;
          case 't': $return.= $this->ndays; break;
          case 'T': $return.= date('T'); break;
          case 'U': $return.= $this->_utime; break;
          case 'w': $return.= $this->wday; break;
          case 'W': $return.= sprintf('%d', ($this->yday + 7 - ($this->wday ? $this->wday - 1 : 6)) / 7); break;
          case 'Y': $return.= sprintf('%04d', $this->year); break;
          case 'y': $return.= sprintf('%02d', $this->year % 100); break;
          case 'z': $return.= $this->yday; break;
          case 'Z': $return.= $gmt * 86400; break;
          case '\\': if ($i++ >= $s) break;
          default: $return.= $format{$i}; break;
        }
      }
      return $return;
    }

    /**
     * Format date
     *
     * @see     php://strftime
     * @param   string format default '%c' format-string
     * @return  string the formatted date
     */
    public function format($format= '%c') {

      // Use builtin?
      if (1971 < $this->year && $this->year < 2038) return strftime($format, $this->_utime);
     
      $return= '';
      if ($token= strtok($format, '%')) do {
        switch ($token{0}) {
          case 'a': $return.= strftime('%a', 86400 * (3 + $result['wday'])); break;
          case 'A': $return.= strftime('%A', 86400 * (3 + $result['wday'])); break;
          case 'b': $return.= strftime('%b', mktime(0, 0, 0, $result['mon'], 2, 1971)); break;
          case 'B': $return.= strftime('%B', mktime(0, 0, 0, $result['mon'], 2, 1971)); break;
          case 'c': $return.= '???PREFERRED???'; break;         // FIXME
          case 'C': $return.= sprintf('%02d', $this->year % 100); break;
          case 'd': $return.= sprintf('%02d', $this->mday); break;
          case 'D': $return.= sprintf('%02d/%02d/%02d', $this->mon, $this->mday, $this->year % 100); break;
          case 'e': $return.= $this->mday; break;
          // case 'g' moved to 'V'
          // case 'G' moved to 'V'
          case 'h': $return.= strftime('%b', mktime(0, 0, 0, $result['mon'], 2, 1971)); break;
          case 'H': $return.= sprintf('%02d', $this->hours); break;
          case 'I': $return.= sprintf('%02d', $this->hours == 0 ? 12 : ($this->hours > 12 ? $this->hours - 12 : $this->hours)); break;
          case 'j': $return.= sprintf('%03d', $this->yday + 1); break;
          case 'm': $return.= sprintf('%02d', $this->mon); break;
          case 'M': $return.= sprintf('%02d', $this->minutes); break;
          case 'n': $return.= "\n"; break;
          case 'p': $return.= $this->hours > 12 ? 'pm' : 'am'; break;
          case 'r': $return.= sprintf(
              '%02d:%02d:%02d %s',
              $this->hours == 0 ? 12 : ($this->hours > 12 ? $this->hours - 12 : $this->hours),
              $this->minutes,
              $this->seconds,
              $this->hours > 12 ? 'PM' : 'AM'
            ); 
            break;
          case 'R': $return.= sprintf('%02d:%02d', $this->hours, $this->minutes); break;
          case 'S': $return.= sprintf('%02d', $this->seconds); break;
          case 't': $return.= "\t"; break;
          case 'T': $return.= sprintf('%02d:%02d:%02d', $this->hours, $this->minutes, $this->seconds); break;
          case 'u': $return.= ($this->wday + 6) % 7; break;
          case 'U': $return.= sprintf('%02d', ($this->yday + 7 - $this->wday) / 7); break;
          case 'g':
          case 'G':
          case 'V': {
          
            // Algorithm from FreeBSD 5.4's /usr/src/lib/libc/stdtime/strftime.c (rev 1.40.2.1)
            $year= $this->year;
            $yday= $this->yday;
            $wday= $this->wday;
            for (;;) {
              $len= (self::_isLeapYear($year) ? 366 : 365);
              
              // What day does the ISO year begin on?
              $bot= (($yday + 11 - $wday) % 7) - 3;
              
              // What day does the next ISO year begin on?
              $top= $bot - ($len % 7);
              if ($top < -3) $top += 7;
              
              $top += $len;
              if ($yday >= $top) { $year++; $w= 1; break; }
              if ($yday >= $bot) { $w= 1 + (($yday - $bot) / 7); break; }
              --$year;
              $yday+= (self::_isLeapYear($year) ? 366 : 365);
            }
            
            switch ($token{0}) {
              case 'g': $return.= sprintf('%02d', $year % 100); break;
              case 'G': $return.= $year; break;
              case 'V': $return.= sprintf('%02d', $w); break;
            }
            
            break;
          }
          case 'W': $return.= sprintf('%02d', ($this->yday + 7 - ($this->wday ? $this->wday - 1 : 6)) / 7); break;
          case 'w': $return.= $this->wday; break;
          case 'x': $return.= '???PREFERRED???'; break;         // FIXME
          case 'X': $return.= '???PREFERRED???'; break;         // FIXME
          case 'y': $return.= sprintf('%02d', $this->year % 100); break;
          case 'Y': $return.= sprintf('%04d', $this->year); break;
          case 'Z': $return.= strftime('%Z'); break;
          default: $return.= $token{1}; break;
        }
        $return.= substr($token, 1);
      } while ($token= strtok('%'));

      return $return;
    }
  }
?>
