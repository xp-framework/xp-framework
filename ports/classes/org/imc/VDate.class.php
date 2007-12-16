<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.Date');

  /**
   * VDate
   *
   * @purpose  Date wrapper for VCalendar
   */
  class VDate extends Object {
    public
      $name=      NULL,
      $date=      NULL,
      $timezone=  NULL;

    /**
     * Constructor
     *
     * @param   mixed arg
     */    
    public function __construct($arg) {
      if (is_object($arg)) {
        $this->date= new Date($arg->_value);
        $this->timezone= $arg->tzid;
      } else {
        $this->date= new Date($arg);
      }
    }
    
    /**
     * Create a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->date->toString('Ymd\THis\Z', new TimeZone('UTC'));
    }
    
    /**
     * Export this VDate
     *
     * @return  string
     */
    public function export() {
      return ($this->name.
        (NULL !== $this->timezone ? ';TZID='.$this->timezone : '').
        ':'.
        $this->toString()
      );
    }
  }
?>
