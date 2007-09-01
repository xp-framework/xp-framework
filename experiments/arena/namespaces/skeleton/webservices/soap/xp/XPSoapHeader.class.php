<?php
/* This class is part of the XP framework
 *
 * $Id: XPSoapHeader.class.php 10015 2007-04-16 16:36:48Z kiesel $ 
 */

  namespace webservices::soap::xp;

  /**
   * SOAP Header interface
   *
   * @see      xp://webservices.soap.SOAPHeaderElement
   * @purpose  Interface
   */
  interface XPSoapHeader {

    /**
     * Retrieve XML representation of this header for use in a SOAP
     * message.
     *
     * @param   array<string, string> ns list of namespaces
     * @return  xml.Node
     */
    public function getNode($ns);
  }
?>
