<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Kapselt die SystemException, die außer der Fehlermeldung
   * noch einen Fehler-Code definiert
   *
   * @see Exception
   */
  class SystemException extends Exception {
    var $code= 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string message Die Fehlermeldung
     * @param   int code Der Fehlercode
     */
    function __construct($message, $code) {
      $this->code= $code;
      parent::__construct($message);
    }
  }
?>
