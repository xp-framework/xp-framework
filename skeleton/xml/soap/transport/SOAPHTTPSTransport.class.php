<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.soap.transport.SOAPHTTPTransport');
  
  /**
   * Dummy class for BC reasons - SoapHttpTransport can handle
   * HTTPS now, too.
   *
   * @deprecated
   * @see         xp://xml.soap.transport.SOAPHTTPTransport
   */
  class SOAPHTTPSTransport extends SOAPHTTPTransport {
  }
?>
