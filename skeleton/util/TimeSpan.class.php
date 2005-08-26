<?php
/* This class is part of the XP framework
 *
 * $Id:  $ 
 */


  /**
   * the class TimeSpan represents a span of time
   *
   * @purpose represents a span of time
   */
  class TimeSpan extends Object {
    var
      $_seconds = 0;


    /**
     * Contructor
     *
     * @access  public
     * @param   int secs - an amount of seconds, absolute value is used
     * @throws  lang.IllegalArgumentException in case the value given is not an int
     */
    function __construct($secs= 0) {
      if (!is_int($secs)) {
        throw(new IllegalArgumentException(
          sprintf('Given argument is not an integer: %s', $secs)
        ));
      } else {
        $this->_seconds= intval(abs($secs));
      }
    }

    /**
     * returns this span of time in seconds
     *
     * @access  public
     * @return  int
     */
    function getSeconds() {
      return $this->_seconds;
    }

    /**
     * returns the amount of 'whole' seconds in this 
     * span of time
     *
     * @access  public
     * @return  int
     */
    function getWholeSeconds() {
      $ts = &new TimeSpan();
      $ts->addDays($this->getWholeDays());
      $ts->addHours($this->getWholeHours());
      $ts->addMinutes($this->getWholeMinutes());
      return $this->_seconds-$ts->getSeconds();
    }
    
    /**
     * returns an amount of minutes less than or equal
     * to this span of time
     *
     * @access  public
     * @return  int
     */
    function getMinutes() {
      return floor($this->_seconds/60);
    }
    
    /**
     * returns a float value representing this span of time
     * in minutes
     *
     * @access  public
     * @return  float
     */
    function getMinutesFloat() {
      return $this->_seconds/60;
    }

    /**
     * returns the amount of 'whole' minutes in this 
     * span of time
     *
     * @access  public
     * @return  int
     */
    function getWholeMinutes() {
      $ts = &new TimeSpan();
      $ts->addDays($this->getWholeDays());
      $ts->addHours($this->getWholeHours());
      return floor(($this->_seconds-$ts->getSeconds())/60);
    }
    
    /**
     * adds an amount of minutes to this span of time
     *
     * @access  public
     * @param   int mins
     */
    function addMinutes($mins) {
      $this->_seconds+= $mins*60;
    }

    /**
     * returns an amount of hours less than or equal
     * to this span of time
     *
     * @access  public
     * @return  int
     */
    function getHours() {
      return floor($this->_seconds/3600);
    }
    
    /**
     * returns a float value representing this span of time
     * in hours
     *
     * @access  public
     * @return  float
     */
    function getHoursFloat() {
      return $this->_seconds/3600;
    }

    /**
     * returns the amount of 'whole' hours in this 
     * span of time
     *
     * @access  public
     * @return  int
     */
    function getWholeHours() {
      $ts = &new TimeSpan();
      $ts->addDays($this->getWholeDays());
      return floor(($this->_seconds-$ts->getSeconds())/3600);
    }
    
    /**
     * adds an amount of Hours to this span of time
     *
     * @access  public
     * @param   int hours
     */
    function addHours($hours) {
      $this->_seconds+= $hours*3600;
    }

    /**
     * returns an amount of days less than or equal
     * to this span of time
     *
     * @access  public
     * @return  int
     */
    function getDays() {
      return floor($this->_seconds/86400);
    }
    
    /**
     * returns a float value representing this span of time
     * in days
     *
     * @access  public
     * @return  float
     */
    function getDaysFloat() {
      return $this->_seconds/86400;
    }

    /**
     * returns the amount of 'whole' days in this 
     * span of time
     *
     * @access  public
     * @return  int
     */
    function getWholeDays() {
      return $this->getDays();
    }

    /**
     * adds an amount of Days to this span of time
     *
     * @access  public
     * @param   int days
     */
    function addDays($days) {
      $this->_seconds+= $days*86400;
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
     * @access  public
     * @param   string format
     * @return  string the formatted timespan
     */
    function format($format) {
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
     * @access  public
     * @see     xp://util.TimeSpan#format
     * @param   string format, defaults to '%ed, %yh, %jm, %ws'
     * @return  string
     */
    function toString($format= '%ed, %yh, %jm, %ws') {
      return $this->format($format);
    }

  }

?>
