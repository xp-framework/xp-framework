<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');

  /**
   * VEvent
   * 
   * @see      xp://org.imc.VCalendar
   * @purpose  Represent a single event
   */
  class VEvent extends Object {
    public
      $date         = NULL,
      $starts       = NULL,
      $ends         = NULL,
      $summary      = '',
      $location     = '',
      $description  = '',
      $attendee     = array(),
      $organizer    = '',
      $uid          = '',
      $priority     = '';

    /**
     * Set UID for Event
     *
     * @param string uid
     */
     public function setUID($uid) {
       $this->uid= $uid;     
     }

    /**
     * Get UID for Event
     *
     * @return string uid   
     */
     public function getUID() {
       return $this->uid;     
     }

    /**
     * Set Date
     *
     * @param   util.Date date
     */
    public function setDate($date) {
      $this->date= $date;
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
     * Set Starts
     *
     * @param   util.Date starts
     */
    public function setStarts($starts) {
      $this->starts= $starts;
    }

    /**
     * Get Starts
     *
     * @return  util.Date
     */
    public function getStarts() {
      return $this->starts;
    }

    /**
     * Set Ends
     *
     * @param   util.Date ends
     */
    public function setEnds($ends) {
      $this->ends= $ends;
    }

    /**
     * Get Ends
     *
     * @return  util.Date
     */
    public function getEnds() {
      return $this->ends;
    }

    /**
     * Set Summary
     *
     * @param   string summary
     */
    public function setSummary($summary) {
      $this->summary= $summary;
    }

    /**
     * Get Summary
     *
     * @return  string
     */
    public function getSummary() {
      return $this->summary;
    }

    /**
     * Set Location
     *
     * @param   string location
     */
    public function setLocation($location) {
      $this->location= $location;
    }

    /**
     * Get Location
     *
     * @return  string
     */
    public function getLocation() {
      return $this->location;
    }

    /**
     * Set Description
     *
     * @param   string description
     */
    public function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get Description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }

    /**
     * Add attendee
     *
     * @param   mixed[] attendee
     */
    public function addAttendee($attendee) {
      $this->attendee[]= $attendee;
    }

    /**
     * Get attendees
     *
     * @return  mixed[]
     */
    public function getAttendees() {
      return $this->attendee;
    }

    /**
     * Set Organizer
     *
     * @param   string organizer
     */
    public function setOrganizer($organizer) {
      $this->organizer= $organizer;
    }

    /**
     * Get Organizer
     *
     * @return  string
     */
    public function getOrganizer() {
      return $this->organizer;
    }
    
    /**
     * Set Priority
     *
     * @param string priority
     */
     public function setPriority($priority) {
       $this->priority= $priority;     
     }

    /**
     * Get Priority for Event
     *
     * @return string
     */
     public function getPriority() {
       return $this->priority;     
     }    

    /**
     * Export function helper
     *
     * @param   string key
     * @param   mixed value
     * @return  string exported
     */    
    protected function _export($key, $value) {
      if ($value instanceof Date) {   // Convert date into string
        $representation= $value->format('%Y%m%dT%H%M%SZ');
      } else if (is_object($value)) {
        foreach (get_object_vars($value) as $pkey => $pvalue) {
          if ('_value' == $pkey) continue;
          
          // Append parameters
          $key.= ';'.strtoupper($pkey).'='.$pvalue;
        }
        $representation= $value->_value;
      } else {                        // Escape string, encode it to UTF8    
        $representation= strtr(utf8_encode($value), array (
          ','   => '\,',
          "\n"  => '\n'
        ));
      }

      return $key.':'.$representation."\r\n";
    }
    
    /**
     * Returns the string representation of this event
     *
     * @return  string event
     */    
    public function export() {
      $ret = $this->_export('BEGIN',       'VEVENT');
      $ret.= $this->_export('LOCATION',    $this->getLocation());
      $ret.= $this->_export('UID',         $this->getUID());
      $ret.= $this->_export('DTSTAMP',     $this->getDate());
      $ret.= $this->_export('DTSTART',     $this->getStarts());
      $ret.= $this->_export('DTEND',       $this->getEnds());
      $ret.= $this->_export('DESCRIPTION', $this->getDescription());
      $ret.= $this->_export('SUMMARY',     $this->getSummary());
      $ret.= $this->_export('ORGANIZER',   $this->getOrganizer());
      $ret.= $this->_export('PRIORITY',    $this->getPriority());
      
      // Append all attendees
      foreach ($this->getAttendees() as $a) {
        $ret.= $this->_export('ATTENDEE', $a);
      }
      
      // Append alarm if existant
      // TODO: Encapsulate this into an own class
      if (isset($this->alarm)) {
        $ret.= $this->_export('BEGIN',       'VALARM');
        $ret.= $this->_export('ACTION',      $this->alarm['action']);
        $ret.= $this->_export('DESCRIPTION', $this->alarm['description']);
        $ret.= $this->_export('TRIGGER',     $this->alarm['trigger']);
        $ret.= $this->_export('END',         'VALARM');
      }
      
      return $ret.$this->_export ('END', 'VEVENT');
    }
  }
?>
