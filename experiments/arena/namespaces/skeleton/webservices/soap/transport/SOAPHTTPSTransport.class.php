<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPHTTPSTransport.class.php 8516 2006-11-20 19:20:03Z friebe $ 
 */

  namespace webservices::soap::transport;

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
