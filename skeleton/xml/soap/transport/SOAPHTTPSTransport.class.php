<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses(
    'xml.soap.transport.SOAPHTTPTransport',
    'net.http.HTTPSRequest'
  );
  
  /**
   * Kapselt den Transport von SOAP-Nachrichten über HTTP
   *
   * @see xml.soap.SOAPClient
   */
  class SOAPHTTPSTransport extends SOAPHTTPTransport {

    /**
     * Constructor
     *
     * @access  public
     * @param   string url Die URL
     */  
    function __construct($url) {
      parent::__construct($url);
      $this->_conn= new HTTPSRequest(array(
        'url' => $url
      ));
    }
 }
?>
