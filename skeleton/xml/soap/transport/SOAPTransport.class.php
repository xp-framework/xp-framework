<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * Kapselt den Transport von SOAP-Nachrichten
   * Dies ist ein "Interface" und soll vererbt werden
   * 
   * @see xml.soap.transport.SOAPHTTPTransport
   */
  class SOAPTransport extends Object {
 
    /**
     * Die SOAP-Message absenden
     *
     * @access  public
     * @param   xml.soap.SOAPMessage message Die zu verschickende Nachricht
     */
   function send(&$message) { }
   
    /**
     * Die SOAP-Antwort auswerten
     *
     * @access  public
     * @return  xml.soap.SOAPMessage Die Antwort
     */
   function retreive() { }
 
 }
?>
