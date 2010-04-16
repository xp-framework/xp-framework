<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');

  define('VCAL_CLASS_PUBLIC',        'PUBLIC');
  define('VCAL_CLASS_PRIVATE',       'PRIVATE');
  define('VCAL_CLASS_CONFINDENTIAL', 'CONFIDENTIAL');

  define('VCAL_TRANSP_OPAQUE',       'OPAQUE');
  define('VCAL_TRANSP_TRANSPARENT',  'TRANSPARENT');   

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
      $priority     = 9,
      $categories   = '',
      $class        = '',
      $transparency = VCAL_TRANSP_OPAQUE,
      $sequence     = '',
      $url          = '',
      $ctype        = NULL;

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
     * Set Sequence for Event
     *
     * @param integer sequence
     */
     public function setSequence($sequence) {
       $this->sequence= $sequence;     
     }

    /**
     * Get Sequence for Event
     *
     * @return integer sequence   
     */
     public function getSequence() {
       return $this->sequence;     
     }
     
    /**
     * Set Category for Event
     *
     * @param string categories
     */
     public function setCategories($categories) {
       $this->categories= $categories;     
     }

    /**
     * Get Category for Event
     *
     * @return string categories
     */
     public function getCategories() {
       return $this->categories;     
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
     * Set Class type
     *
     * @param   string ctype one of the VCAL_CLASS_* constants
     */
    public function setClasstype($ctype) {
      $this->ctype= $ctype;
    }

    /**
     * Get Class
     *
     * @return  string 
     */
    public function getClasstype() {
      return $this->ctype;
    }

    /**
     * Set Time Transparency
     *
     * @param   string transp
     */
    public function setTransparency($transparency) {
      $this->transparency= $transparency;
    }

    /**
     * Get Time Transparency
     *
     * @return  string 
     */
    public function getTransparency() {
      return $this->transparency;
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
     * @param   mixed attendee
     */
    public function addAttendee($attendee) {
      $this->attendee[]= $attendee;
    }

    /**
     * Set attendees
     *
     * @param   mixed[] attendees
     */
    public function setAttendees($attendees) {
      $this->attendee[]= $attendees;
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
     * Get number of attendees
     *
     * @return  int
     */
    public function numAttendees() {
      return sizeof($this->attendee);
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
     * Set URL for Event
     *
     * @param string url
     */
     public function setURL($url) {
       $this->url= $url;
     }

    /**
     * Get URL for Event
     *
     * @return string url
     */
     public function getURL() {
       return $this->url;
     }

    /**
     * Export function helper
     *
     * @param   string key
     * @param   mixed value
     * @param   bool empty default TRUE whether to export empty elements
     * @return  string exported
     */    
    protected function _export($key, $value, $empty= TRUE) {
      if (!$empty && '' == $value) return '';
      
      if ($value instanceof Date) {   // Convert date into string
        $representation= $value->format('%Y%m%dT%H%M%SZ', new TimeZone('UTC'));
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

      if ((!is_object($value)) && strstr($value, '=')) {
      
        return $key.';'.$representation."\r\n";
      
      } else {
          return $key.':'.$representation."\r\n";
      }
    }

    /**
     * Returns the string representation of this event
     *
     * @return  string event
     */    
    public function export() {

      // TODO: URL must be exported
      $ret= (
        $this->_export('BEGIN',       'VEVENT').
        $this->_export('LOCATION',    $this->getLocation()).
        $this->_export('UID',         $this->getUID()).
        $this->_export('SEQUENCE',    $this->getSequence(), FALSE).
        $this->_export('DTSTAMP',     $this->getDate()).
        $this->_export('DTSTART',     $this->getStarts()).
        $this->_export('DTEND',       $this->getEnds(), FALSE).
        $this->_export('DESCRIPTION', $this->getDescription()).
        $this->_export('SUMMARY',     $this->getSummary()).
        $this->_export('CATEGORIES',  $this->getCategories(), FALSE).
        $this->_export('CLASS',       $this->getClasstype(), FALSE).
        $this->_export('TRANSP',      $this->getTransparency()).
        $this->_export('ORGANIZER',   $this->getOrganizer(), FALSE).
        $this->_export('PRIORITY',    $this->getPriority())
      );
      
      // Append all attendees
      foreach ($this->getAttendees() as $a) {
        $ret.= $this->_export('ATTENDEE', $a);
      }
      
      // Append alarm if existant
      // TODO: Encapsulate this into an own class
      if (isset($this->alarm)) {
        $ret.= (
          $this->_export('BEGIN',       'VALARM').
          $this->_export('ACTION',      $this->alarm['action']).
          $this->_export('DESCRIPTION', $this->alarm['description']).
          $this->_export('TRIGGER',     $this->alarm['trigger']).
          $this->_export('END',         'VALARM')
        );
      }
      
      return $ret.$this->_export ('END', 'VEVENT');
    }
  }
?>
