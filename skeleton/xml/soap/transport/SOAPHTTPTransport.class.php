<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses(
    'xml.soap.transport.SOAPTransport', 
    'xml.soap.SOAPFaultException', 
    'net.http.HTTPRequest'
  );
  
  /**
   * Kapselt den Transport von SOAP-Nachrichten über HTTP
   *
   * @see xml.soap.SOAPClient
   */
  class SOAPHTTPTransport extends SOAPTransport {
    var
      $_conn,
      $_action;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string url Die URL
     */  
    function __construct($url) {
      $this->_conn= new HTTPRequest(array(
        'url' => $url
      ));
      parent::__construct();
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->_conn->__destruct();
      parent::__destruct();
    }

    /**
     * Die SOAP-Message absenden
     *
     * @access  public
     * @param   xml.soap.SOAPMessage message Die zu verschickende Nachricht
     * @throws  IllegalArgumentException, wenn message keine SOAPMessage ist
     */
    function send(&$message) {
      if (!is_a($message, 'SOAPMessage')) return throw(new IllegalArgumentException(
        'parameter "message" must be a xml.soap.SOAPMessage'
      ));

      // SOAP-Action-Header
      $this->_conn->headers['SOAPAction']= '"'.$message->action.'#'.$message->method.'"';
      
      // Content-Type
      $this->_conn->contentType= 'text/xml; charset=iso-8859-1';
      
      // Action merken
      $this->action= $message->action;

      // Request absenden
      return $this->_conn->post(
        '#'.XML_DECLARATION.
        $message->getSource(0)
      );
   }
   
    /**
     * Die SOAP-Antwort auswerten
     *
     * @access  public
     * @return  xml.soap.SOAPMessage Die Antwort
     */
   function retreive() {
   
      // Rückgabe auswerten
      $answer= new SOAPMessage();
      
      // Auf das Encoding achten!
      if (isset($this->_conn->response->ContentType)) {
        @list($type, $charset)= explode('; charset=', $this->_conn->response->ContentType);
        if (!empty($charset)) $answer->encoding= $charset;
      }
      
      $answer->action= $this->action;
      try(); {
        $answer->fromString($this->_conn->response->body);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Nach Fault checken
      if (intval($this->_conn->response->HTTPstatus) != 200) {
        if (NULL !== ($fault= $answer->getFault())) {
          throw(new SOAPFaultException($fault));
          return $answer;
        } else {
          return throw(new Exception($this->response->HTTPmessage));
        }
      }
      
      return $answer;
   }
 
 }
?>
