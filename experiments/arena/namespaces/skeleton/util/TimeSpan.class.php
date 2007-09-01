<?php
/* This class is part of the XP framework
 *
 * $Id: TimeSpan.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace util;

  /**
   * Represents a span of time
   *
   * @see      xp://util.DateUtil#timespanBetween
   * @purpose  Time and date
   */
  class TimeSpan extends lang::Object {
    public
      $_seconds = 0;


    /**
     * Contructor
     *
     * @param   int secs - an amount of seconds, absolute value is used
     * @throws  lang.IllegalArgumentException in case the value given is not numeric
     */
    public function __construct($secs= 0) {
      if (!is_numeric($secs)) {
        throw (new lang::IllegalArgumentException(
          'Given argument is not an integer: '.::xp::stringOf($secs)
        ));
        return;
      }
      $this->_seconds= (int)abs($secs);
    }

    /**
     * returns this span of time in seconds
     *
     * @return  int
     */
    public function getSeconds() {
      return $this->_seconds;
    }

    /**
     * returns the amount of 'whole' seconds in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeSeconds() {
      $ts= new TimeSpan();
      $ts->addDays($this->getWholeDays());
      $ts->addHours($this->getWholeHours());
      $ts->addMinutes($this->getWholeMinutes());
      return $this->_seconds- $ts->getSeconds();
    }
    
    /**
     * returns an amount of minutes less than or equal
     * to this span of time
     *
     * @return  int
     */
    public function getMinutes() {
      return floor($this->_seconds / 60);
    }
    
    /**
     * returns a float value representing this span of time
     * in minutes
     *
     * @return  float
     */
    public function getMinutesFloat() {
      return $this->_seconds / 60;
    }

    /**
     * returns the amount of 'whole' minutes in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeMinutes() {
      $ts = new TimeSpan();
      $ts->addDays($this->getWholeDays());
      $ts->addHours($this->getWholeHours());
      return floor(($this->_seconds- $ts->getSeconds()) / 60);
    }
    
    /**
     * adds an amount of minutes to this span of time
     *
     * @param   int mins
     */
    public function addMinutes($mins) {
      $this->_seconds+= $mins * 60;
    }

    /**
     * returns an amount of hours less than or equal
     * to this span of time
     *
     * @return  int
     */
    public function getHours() {
      return floor($this->_seconds / 3600);
    }
    
    /**
     * returns a float value representing this span of time
     * in hours
     *
     * @return  float
     */
    public function getHoursFloat() {
      return $this->_seconds / 3600;
    }

    /**
     * returns the amount of 'whole' hours in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeHours() {
      $ts= new TimeSpan();
      $ts->addDays($this->getWholeDays());
      return floor(($this->_seconds- $ts->getSeconds()) / 3600);
    }
    
    /**
     * adds an amount of Hours to this span of time
     *
     * @param   int hours
     */
    public function addHours($hours) {
      $this->_seconds+= $hours * 3600;
    }

    /**
     * returns an amount of days less than or equal
     * to this span of time
     *
     * @return  int
     */
    public function getDays() {
      return floor($this->_seconds / 86400);
    }
    
    /**
     * returns a float value representing this span of time
     * in days
     *
     * @return  float
     */
    public function getDaysFloat() {
      return $this->_seconds / 86400;
    }

    /**
     * returns the amount of 'whole' days in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeDays() {
      return $this->getDays();
    }

    /**
     * adds an amount of Days to this span of time
     *
     * @param   int days
     */
    public function addDays($days) {
      $this->_seconds+= $days * 86400;
    }

    /**
     * Format timespan
     *
     * Format tokens are:
     * <pre>
     * %s   - seconds
     * %w   - 'whole' seconds
     * %m   - minutes
     * %M   - minutes (float)
     * %j   - 'whole' minutes
     * %h   - hours
     * %H   - hours (float)
     * %y   - 'whole' hours
     * %d   - days
     * %D   - days (float)
     * %e   - 'whole' days
     * </pre>
     *
     * @param   string format
     * @return  string the formatted timespan
     */
    public function format($format) {
      $return= '';

      $tok= strtok($format, '%');
      // iterate on tokens
      while (FALSE !== $tok) {
        switch ($tok{0}) {
          case 's':
            $return.= $this->getSeconds();
            break;
          case 'w':
            $return.= $this->getWholeSeconds();
            break;
          case 'm':
            $return.= $this->getMinutes();
            break;
          case 'M':
            $return.= sprintf('%.2f', $this->getMinutesFloat());
            break;
          case 'j':
            $return.= $this->getWholeMinutes();
            break;
          case 'h':
            $return.= $this->getHours();
            break;
          case 'H':
            $return.= sprintf('%.2f', $this->getHoursFloat());
            break;
          case 'y':
            $return.= $this->getWholeHours();
            break;
          case 'd':
            $return.= $this->getDays();
            break;
          case 'D':
            $return.= sprintf('%.2f', $this->getDaysFloat());
            break;
          case 'e':
            $return.= $this->getWholeDays();
            break;
          default:
            $return.= '%'.$tok{0};
        }
        $return.= substr($tok, 1);
        $tok= strtok('%');
      }
     
      return $return;
    }

    /**
     * creates a string representation
     *
     * @see     xp://util.TimeSpan#format
     * @param   string format, defaults to '%ed, %yh, %jm, %ws'
     * @return  string
     */
    public function toString($format= '%ed, %yh, %jm, %ws') {
      return $this->format($format);
    }
  }
?>
