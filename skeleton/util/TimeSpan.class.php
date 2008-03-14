<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Represents a span of time
   *
   * @see      xp://util.DateUtil#timespanBetween
   * @test     xp://net.xp_framework.unittest.util.TimeSpanTest
   * @purpose  Time and date
   */
  class TimeSpan extends Object {
    protected
      $_seconds = 0;
    
    /**
     * Contructor
     *
     * @param   int secs - an amount of seconds, absolute value is used
     * @throws  lang.IllegalArgumentException in case the value given is not numeric
     */
    public function __construct($secs= 0) {
      if (!is_numeric($secs)) {
        throw (new IllegalArgumentException(
          'Given argument is not an integer: '.xp::stringOf($secs)
        ));
        return;
      }
      $this->_seconds= (int)abs($secs);
    }

    /**
     * Add a TimeSpan
     *
     * @param   util.TimeSpan... args
     * @return  util.TimeSpan
     */
    public function add() {
      foreach (func_get_args() as $span) {
        if (!$span instanceof self) {
          throw (new IllegalArgumentException(
            'Given argument is not a TimeSpan: '.xp::stringOf($span)
          ));
        }

        $this->_seconds+= $span->_seconds;
      }
      
      return $this;
    }

    /**
     * Substract a TimeSpan
     *
     * @param   util.TimeSpan... args
     * @return  util.TimeSpan
     */
    public function substract() {
      foreach (func_get_args() as $span) {
        if (!$span instanceof self) {
          throw (new IllegalArgumentException(
            'Given argument is not a TimeSpan: '.xp::stringOf($span)
          ));
        }

        $this->_seconds-= $span->_seconds;
      }
      
      return $this;
    }

    /**
     * Get timespan from seconds
     *
     * @param   int seconds
     * @return  util.TimeSpan
     */
    public static function seconds($seconds) {
      return new self($seconds);
    }

    /**
     * Get timespan from minutes
     *
     * @param   int minutes
     * @return  util.TimeSpan
     */
    public static function minutes($minutes) {
      return new self($minutes * 60);
    }

    /**
     * Get timespan from hours
     *
     * @param   int hours
     * @return  util.TimeSpan
     */
    public static function hours($hours) {
      return new self($hours * 3600);
    }

    /**
     * Get timespan from days
     *
     * @param   int days
     * @return  util.TimeSpan
     */
    public static function days($days) {
      return new self($days * 86400);
    }

    /**
     * Get timespan from weeks
     *
     * @param   int weeks
     * @return  util.TimeSpan
     */
    public static function weeks($weeks) {
      return new self($weeks * 604800);
    }

    /**
     * Returns this span of time in seconds
     *
     * @return  int
     */
    public function getSeconds() {
      return $this->_seconds;
    }

    /**
     * Returns the amount of 'whole' seconds in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeSeconds() {
      return $this->_seconds % 60;
    }
    
    /**
     * Return an amount of minutes less than or equal
     * to this span of time
     *
     * @return  int
     */
    public function getMinutes() {
      return (int)floor($this->_seconds / 60);
    }
    
    /**
     * Returns a float value representing this span of time
     * in minutes
     *
     * @return  float
     */
    public function getMinutesFloat() {
      return $this->_seconds / 60;
    }

    /**
     * Returns the amount of 'whole' minutes in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeMinutes() {
      return (int)floor(($this->_seconds % 3600) / 60);
    }
    
    /**
     * Adds an amount of minutes to this span of time
     *
     * @param   int mins
     * @deprecated
     */
    public function addMinutes($mins) {
      $this->_seconds+= (int)$mins * 60;
    }

    /**
     * Returns an amount of hours less than or equal
     * to this span of time
     *
     * @return  int
     */
    public function getHours() {
      return (int)floor($this->_seconds / 3600);
    }
    
    /**
     * Returns a float value representing this span of time
     * in hours
     *
     * @return  float
     */
    public function getHoursFloat() {
      return $this->_seconds / 3600;
    }

    /**
     * Returns the amount of 'whole' hours in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeHours() {
      return (int)floor(($this->_seconds % 86400) / 3600);
    }
    
    /**
     * Adds an amount of Hours to this span of time
     *
     * @param   int hours
     * @deprecated
     */
    public function addHours($hours) {
      $this->_seconds+= (int)$hours * 3600;
    }

    /**
     * Returns an amount of days less than or equal
     * to this span of time
     *
     * @return  int
     */
    public function getDays() {
      return (int)floor($this->_seconds / 86400);
    }
    
    /**
     * Returns a float value representing this span of time
     * in days
     *
     * @return  float
     */
    public function getDaysFloat() {
      return $this->_seconds / 86400;
    }

    /**
     * Returns the amount of 'whole' days in this 
     * span of time
     *
     * @return  int
     */
    public function getWholeDays() {
      return $this->getDays();
    }

    /**
     * Adds an amount of Days to this span of time
     *
     * @param   int days
     * @deprecated
     */
    public function addDays($days) {
      $this->_seconds+= (int)$days * 86400;
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
