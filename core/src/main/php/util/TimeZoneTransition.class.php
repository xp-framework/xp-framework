<?php
/* This class is part of the XP framework
 *
 * $Id: TimeZoneTransition.class.php 10948 2007-08-24 14:24:55Z kiesel $ 
 */

  /**
   * Represent a timezone transition.
   *
   * @see   xp://util.TimeZone
   * @test  xp://net.xp_framework.unittest.util.TimeZoneTest
   */
  class TimeZoneTransition extends Object {
    protected
      $tz     = NULL,
      $date   = NULL,
      $isDst  = NULL,
      $offset = NULL,
      $abbr   = NULL;

    /**
     * Constructor
     *
     * @param   util.TimeZone tz
     * @param   util.Date date
     */
    public function __construct(TimeZone $tz, Date $date) {
      $this->tz= $tz;
      $this->date= $date;
    }
    
    /**
     * Retrieve the next timezone transition for the timezone tz
     * after date date.
     *
     * @param   util.TimeZone tz
     * @param   util.Date date
     * @return  util.TimeZoneTransition
     * @throws  lang.IllegalArgumentException if timezone has no transitions
     */
    public static function nextTransition(TimeZone $tz, Date $date) {
      $t= new self($tz, $date);
      $t->next();
      return $t;
    }
    
    /**
     * Retrieve the previous timezone transition for the timezone tz
     * before date date.
     *
     * @param   util.TimeZone tz
     * @param   util.Date date
     * @return  util.TimeZoneTransition
     * @throws  lang.IllegalArgumentException if timezone has no transitions
     */
    public static function previousTransition(TimeZone $tz, Date $date) {
      $t= new self($tz, $date);
      $t->previous();
      return $t;
    }
    
    /**
     * Seek to the next timezone transition
     *
     * @throws  lang.IllegalArgumentException if timezone has no transitions
     */
    public function next() {
      $ts= $this->date->getTime();
      foreach (timezone_transitions_get($this->tz->getHandle()) as $t) {
        if ($t['ts'] > $ts) break;
      }
      if (!isset($t)) throw new IllegalArgumentException('Timezone '.$this->tz->getName().' does not have DST transitions.');
      
      $this->date= new Date($t['ts']);
      $this->isDst= $t['isdst'];
      $this->offset= $t['offset'];
      $this->abbr= $t['abbr'];
    }

    /**
     * Seek to the previous timezone transition
     *
     * @throws  lang.IllegalArgumentException if timezone has no transitions
     */
    public function previous() {
      $ts= $this->date->getTime();
      foreach (timezone_transitions_get($this->tz->getHandle()) as $t) {
        if ($t['ts'] >= $ts) break;
        $last= $t;
      }
      if (!isset($t)) throw new IllegalArgumentException('Timezone '.$this->tz->getName().' does not have DST transitions.');

      $this->date= new Date($last['ts'], new TimeZone($last['abbr']));
      $this->isDst= $last['isdst'];
      $this->offset= $last['offset'];
      $this->abbr= $last['abbr'];
    }
    
    /**
     * Get Tz
     *
     * @return  util.TimeZone
     */
    public function getTz() {
      return $this->tz;
    }

    /**
     * Get Date
     *
     * @return  util.Date
     */
    public function getDate() {
      return $this->date;
    }

    /**
     * Get IsDst
     *
     * @return  bool
     */
    public function isDst() {
      return $this->isDst;
    }

    /**
     * Get Offset
     *
     * @return  int
     */
    public function offset() {
      return $this->offset;
    }

    /**
     * Get Abbr
     *
     * @return  string
     */
    public function abbr() {
      return $this->abbr;
    }
    
    /**
     * Create string representation of transition
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'('.$this->hashCode().")@{\n";
      $s.= '  transition at: '.$this->date->toString()."\n";
      $s.= sprintf('  transition to: %s (%s), %s',
        $this->offset,
        $this->abbr,
        ($this->isDst ? 'DST' : 'non-DST')
      );
      return $s."\n}";
    }
  }
?>
