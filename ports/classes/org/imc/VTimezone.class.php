<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Date',
    'peer.URL'
  );
  
  // Identifier
  define('VTZ_ID',       'VTIMEZONE');
  
  /**
   * VTimezone
   *
   * @purpose  Timezone wrapper for VCalendar
   */
  class VTimezone extends Object {
    public
      $tzid=      '',
      $daylight=  NULL,
      $standard=  NULL,
      $tzurl=     NULL,
      $lastmod=   NULL;

    /**
     * Constructor
     *
     */
    public function __construct($tzid= '') {
      $this->daylight= $this->standard= array (
        'dtstart'       => NULL,    // Mandatory
        'tzoffsetto'    => NULL,    // Mandatory
        'tzoffsetfrom'  => NULL,    // Mandatory
        'comment'       => NULL,
        'rdate'         => NULL,
        'rrule'         => NULL,
        'tzname'        => NULL,
        'x-prop'        => NULL,
      );
      $this->setTzid($tzid);
    }

    /**
     * Sets the unique tz-identifier (unique for this icalendar)
     *
     * @param   string id
     */    
    public function setTzid($id) {
      $this->tzid= $id;
    }

    /**
     * Returns the tz-identifier
     *
     * @return  string id
     */    
    public function getTzid() {
      return $this->tzid;
    }
    /**
     * Sets the Timezone url
     *
     * @param   peer.URL url
     */    
    public function setTZUrl($url) {
      $this->tzurl= $url;
    }

    /**
     * Gets the Timezone url
     *
     * @return  peer.URL url
     */    
    public function getTZUrl() {
      return $this->tzurl;
    }

    /**
     * Sets the last modification time
     *
     * @param   util.Date date
     */    
    public function setLastMod($date) {
      $this->lastmod= $date;
    }

    /**
     * Gets the last modification time
     *
     * @return  util.Date date
     */    
    public function getLastMod() {
      return $this->lastmod;
    }

    /**
     * Export function helper
     *
     * @param   string key
     * @param   mixed value
     * @return  string exported
     */    
    protected function _export($key, $value) {

      // Never add empty fields
      if (NULL === $value)
        return '';
        
      if (is('Date', $value)) {
        // Convert date into string
        $value= $value->toString ('Ymd').'T'.$value->toString ('His').'Z';
      } else if (is('URL', $value)) {
        // Convert URL to string
        $value= $value->toString();
      }

      // Escape string, encode it to UTF8    
      return ($key.':'.strtr(utf8_encode ($value), array (
        ','   => '\,',
        "\n"  => '\r\n'
      ))."\n");
    }
    
    /**
     * Return a string representation of this object suitable
     * for inclusion in an VCalendar
     *
     * @return  string representation
     */    
    public function export() {
      $ret = $this->_export ('BEGIN',     VTZ_ID);
      $ret.= $this->_export ('TZID',      $this->getTzid());

      $ret.= $this->_export ('TZURL',     $this->getTZurl());
      $ret.= $this->_export ('LAST-MOD',  $this->getLastMod());
      
      foreach (array ('daylight', 'standard') as $type) {
        // Begin daylight/standard part
        $ret.= $this->_export ('BEGIN', strtoupper ($type));
        
        // Export all attributes
        foreach ($this->{$type} as $key => $value) {
          if ($value) { $ret.= $this->_export (strtoupper ($key), $value); }
        }
        
        // End daylight/standard part
        $ret.= $this->_export ('END', strtoupper ($type));
      }
      
      $ret.= $this->_export ('END', VTZ_ID);
      
      return $ret;
    }
  }
?>
