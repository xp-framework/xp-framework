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
    /**
     * Sets the Timezone url
     *
     * @access  public
     * @param   &peer.URL url
     */    
    function setTZUrl(&$url) {
      $this->tzurl= &$url;
    }

    /**
     * Gets the Timezone url
     *
     * @access  public
     * @return  &peer.URL url
     */    
    function &getTZUrl() {
      return $this->tzurl;
    }

    /**
     * Sets the last modification time
     *
     * @access  public
     * @param   &util.Date date
     */    
    function setLastMod(&$date) {
      $this->lastmod= &$date;
    }

    /**
     * Gets the last modification time
     *
     * @access  public
     * @return  &util.Date date
     */    
    function &getLastMod() {
      return $this->lastmod;
    }
  }
?>
