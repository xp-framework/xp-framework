<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.soap.transport.SOAPHTTPTransport');
  
  /**
   * Dummy class for BC reasons - SoapHttpTransport can handle
   * HTTPS now, too.
   *
   * @deprecated
   * @see         xp://webservices.soap.transport.SOAPHTTPTransport
   */
  class SOAPHTTPSTransport extends SOAPHTTPTransport {
  }
?>
