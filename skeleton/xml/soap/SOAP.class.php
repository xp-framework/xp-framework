<?php
  /**
   * SOAP Helperfunktionen
   *
   * @see http://www.w3.org/TR/xmlschema-2
   */
  class SOAP extends Object {
  
    /**
     * Gibt Datum/Uhrzeit ISO-8601 konform zurück
     *
     * @access  public
     * @param   int utime
     * @return  string ISO-8601-konformes Datums/Uhrzeitformat
     * @see     http://www.w3.org/TR/xmlschema-2/#ISO8601 
     * @see     http://www.w3.org/TR/xmlschema-2/#dateTime
     */
    function dateTime($utime) {
      return 'dateTime\1'.date(
        'Y-m-d\TH:i:s', 
        $utime
      );
    }
  }
?>
