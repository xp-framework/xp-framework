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
  class SystemException extends XPException {
    public $code= 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string message Die Fehlermeldung
     * @param   int code Der Fehlercode
     */
    public function __construct($message, $code) {
      $this->code= $code;
      parent::__construct($message);
    }
  }
?>
