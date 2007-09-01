<?php
/* This class is part of the XP framework
 *
 * $Id: VDate.class.php 9398 2007-01-31 14:00:47Z rene $
 */

  namespace org::imc;

  ::uses('util.Date');

  /**
   * VDate
   *
   * @purpose  Date wrapper for VCalendar
   */
  class VDate extends lang::Object {
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
        $this->date= new util::Date(text::parser::VFormatParser::decodeDate($arg->_value));
        $this->timezone= $arg->tzid;
      } else {
        $this->date= new util::Date(text::parser::VFormatParser::decodeDate($arg));
      }
    }
    
    /**
     * Create a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->date->format('%Y%m%dT%H%M%SZ')
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
