<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'util.Date',
    'peer.URL'
  );
  
  class VTimezone extends Object {
    var
      $tzid=      '',
      $daylight=  NULL,
      $standard=  NULL,
      $tzurl=     NULL,
      $lastmod=   '';

    /**
     * Construcotr
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->daylight= $this->standard= array (
        'dtstart'       => NULL,    // Mandatory
        'tzoffsetto'    => NULL,    // Mandatory
        'tzoffsetfrom'  => NULL,    // Mandatory
        'comment'       => '',
        'rdate'         => '',
        'rrule'         => '',
        'tzname'        => '',
        'x-prop'        => ''
      );
    }

    /**
     * Sets the unique tz-identifier (unique for this icalendar)
     *
     * @access  public
     * @param   string id
     */    
    function setTzid($id) {
      $this->tzid= $id;
    }

    /**
     * Returns the tz-identifier
     *
     * @access  public
     * @return  string id
     */    
    function getTzid() {
      return $this->tzid;
    }
    
    function setTZUrl(&$url) {
      $this->tzurl= &$url;
    }
    
    function &getTZUrl() {
      return $this->tzurl;
    }
      

  
  }
?>
