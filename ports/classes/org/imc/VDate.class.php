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
     * @param   &mixed arg
     */    
    public function __construct($arg) {
      if (is_object($arg)) {
        $this->date= new Date (VFormatParser::decodeDate($arg->_value));
        $this->timezone= $arg->tzid;
      } else {
        $this->date= new Date (VFormatParser::decodeDate($arg));
      }
    }
    
    /**
     * Create a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->date->toString ('Ymd').'T'.$this->date->toString ('His').'Z';
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
