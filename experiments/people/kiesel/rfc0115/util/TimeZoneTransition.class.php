<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class TimeZoneTransition extends Object {
    protected
      $tz     = NULL,
      $date   = NULL,
      $isDst  = NULL,
      $offset = NULL,
      $abbr   = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct(TimeZone $tz, Date $date) {
      $this->tz= $tz;
      $this->date= $date;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function nextTransition(TimeZone $tz, Date $date) {
      $t= new self($tz, $date);
      $t->next();
      return $t;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function previousTransition(TimeZone $tz, Date $date) {
      $t= new self($tz, $date);
      $t->previous();
      return $t;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function previous() {
      $ts= $this->date->getTime();
      foreach (timezone_transitions_get($this->tz->getHandle()) as $t) {
        if ($t['ts'] > $ts) break;
        $last= $t;
      }
      if (!isset($t)) throw new IllegalArgumentException('Timezone '.$this->tz->getName().' does not have DST transitions.');
      
      $this->date= new Date($last['ts']);
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
  }
?>
