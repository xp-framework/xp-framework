<?php
  /**
   * Repräsetiert SOAP-Datum
   *
   * @see     http://www.w3.org/TR/xmlschema-2/#ISO8601 
   * @see     http://www.w3.org/TR/xmlschema-2/#dateTime
   */
  class SOAPDateTime extends Object {
    var $utime;
    
    /**
     + Constructor
     */
    function __construct($params= NULL) {
      if (is_int($params)) $params['utime']= $params;
      Object::__construct($params);
    }
    
    /**
     * Gibt Datum/Uhrzeit ISO-8601 konform als String zurück
     *
     * @access  public
     * @return  string ISO-8601-konformes Datums/Uhrzeitformat
     */
    function toString() {
      return date('Y-m-d\TH:i:s', $this->utime);
    }
    
    /**
     * Typ-Name
     *
     * @access  public
     * @return  string Typ-Namen
     */
    function getType() {
      return 'dateTime';
    }
  }
?>
