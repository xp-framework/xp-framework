<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */

  /**
   * Kapselt Datum und Uhrzeit
   * 
   * Felder:
   * <pre>
   * seconds	=> Sekunden
   * minutes	=> Minuten 
   * hours	=> Stunden
   * mday	=> Tag des Monats
   * wday	=> Tag der Woche (numerisch): 0 = Sonntag, ..., 6 = Samstag
   * mon	=> Monat
   * year	=> Jahr
   * yday	=> Tag des Jahres
   * weekday	=> Tag der Woche (textuell, lang), bspw. "Friday"
   * month	=> Monat (textuell, lang), bspw. "January" 
   * </pre>
   */
  class Date extends Object {
    var
      $_utime;
      
    var
      $seconds,	
      $minutes,	
      $hours,	
      $mday,	
      $wday,	
      $mon,	
      $year,	
      $yday,	
      $weekday,	
      $month;	

    /**
     * Constructor
     *
     * @param mixed in Entweder ein Unix-TimeStamp oder ein String
     */
    function __construct($in= NULL) {
      if (is_string($in)) $this->fromString($in);
      if (is_int($in)) $this->_utime($in);
      parent::__construct();
    }
    
    /**
     * Erstellt ein Datum aus einem String
     *
     * @see     http://php.net/strtotime
     * @param   string str Der Eingabe-String
     */
    function fromString($str) {
      $this->_utime(strtotime($str));
    }
    
    /**
     * Private Helper-Funktion
     *
     * @access  private
     * @param   int utime Millisekunden seit 1970
     */
    function _utime($utime) {
      $a= getdate($this->_utime= $utime);
      foreach ($a as $key=> $val) if (is_string($key)) $this->$key= $val;
    }
    
    /**
     * Vergleichsfunktion
     *
     * @param   Date date Ein Date-Objekt
     * @return  int gleich: 0, date vor $this: <0, date nach $this: >0
     */
    function compareTo($date) {
      return strcmp(
        (string)$date->_utime, 
        (string)$this->_utime
      );
    }
    
    /**
     * String-Repräsentation eines Datums
     *
     * @see     http://php.net/date
     * @param   string format default 'r' Format-String
     * @return  string Das formatierte Datum
     */
    function toString($format= 'r') {
      return date($format, $this->_utime);
    }
  }
?>
