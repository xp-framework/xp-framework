<?php
  uses('util.Date');
  
  /**
   * Repräsetiert SOAP-Datum
   *
   * @see     http://www.w3.org/TR/xmlschema-2/#ISO8601 
   * @see     http://www.w3.org/TR/xmlschema-2/#dateTime
   */
  class SOAPDateTime extends Date {
    
    /**
     * Gibt Datum/Uhrzeit ISO-8601 konform als String zurück
     *
     * @access  public
     * @return  string ISO-8601-konformes Datums/Uhrzeitformat
     */
    function toString() {
      return date('Y-m-d\TH:i:s', $this->_utime);
    }
    
    /**
     * Typ-Name
     *
     * @access  public
     * @return  string Typ-Namen
     */
    function getType() {
      return 'xsd:dateTime';
    }
    
    function getItemName() {
      return FALSE;
    }
  }
?>
