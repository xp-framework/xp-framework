<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.soap.transport.SOAPTransport', 
    'xml.soap.SOAPFaultException', 
    'peer.http.HttpConnection'
  );
  
  /**
   * HTTP transport. Also handles HTTPS.
   *
   * @purpose  Transport SOAP messages
   * @see      xp://xml.soap.SOAPClient
   */
  class SOAPHTTPTransport extends SOAPTransport {
    var
      $_conn,
      $_action;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string url
     */  
    function __construct($url) {
      $this->_conn= &new HttpConnection($url);
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
     * Send the message
     *
     * @access  public
     * @param   &xml.soap.SOAPMessage message
     * @throws  IllegalArgumentException in case the given parameter is not a xml.soap.SOAPMessage
     */
    function &send(&$message) {
    
      // Sanity checks
      if (!is_a($message, 'SOAPMessage')) return throw(new IllegalArgumentException(
        'parameter "message" must be a xml.soap.SOAPMessage'
      ));
      if (!$this->_conn->request) return throw(new IllegalArgumentException(
        'Factory method failed'
      ));

      // Action
      $this->action= $message->action;

      // Post XML
      $this->_conn->request->setMethod(HTTP_POST);
      $this->_conn->request->setParameters(new RequestData(
        $message->getDeclaration()."\n".
        $message->getSource(0)
      ));
      $this->_conn->request->setHeader('SOAPAction', '"'.$message->action.'#'.$message->method.'"');
      $this->_conn->request->setHeader('Content-Type', 'text/xml; charset='.$message->getEncoding());
      
      try(); {
        $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
        $res= &$this->_conn->request->send();
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
      
      return $res;
   }
   
    /**
     * Retrieve the answer
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage
     */
    function &retrieve(&$response) {

      // HACK: Read statuscode, so all headers are read before $response
      // is dumped. Otherwise the result is b0rked.
      $response->getStatusCode();
      $this->cat && $this->cat->debug('<<<', $response);
      
      try(); {
        $xml= '';
        while ($buf= $response->readData()) $xml.= $buf;
        
        $this->cat && $this->cat->debug('<<<', $xml);
        if ($answer= &SOAPMessage::fromString($xml)) {

          // Check encoding
          if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
            @list($type, $charset)= explode('; charset=', $content_type);
            if (!empty($charset)) $answer->setEncoding($charset);
          }

          $answer->action= $this->action;
        }
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Fault?
      if (NULL !== ($fault= $answer->getFault())) {
        return throw(new SOAPFaultException($fault));
      }
      
      // HTTP_OK return code?
      if (200 != $response->getStatusCode()) {
        return throw(new Exception('Unexpected return code: '.$response->getStatusCode()));
      }
      
      return $answer;
   }
 
 }
?>
