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
    protected
      $_conn,
      $_action;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string url
     */  
    public function __construct($url) {
      $this->_conn= new HttpConnection($url);
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      $this->_conn->__destruct();
      parent::__destruct();
    }
    
    /**
     * Create a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return sprintf('%s { %s }', self::getClassName(), $this->_conn->request->url->_info['url']);
    }

    /**
     * Send the message
     *
     * @access  public
     * @param   &xml.soap.SOAPMessage message
     * @throws  IllegalArgumentException in case the given parameter is not a xml.soap.SOAPMessage
     */
    public function send(SOAPMessage $message) {
    
      // Sanity checks
      if (!is_a($message, 'SOAPMessage')) throw (new IllegalArgumentException(
        'parameter "message" must be a xml.soap.SOAPMessage'
      ));
      if (!$this->_conn->request) throw (new IllegalArgumentException(
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
      
      try {
        $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
        $res= $this->_conn->request->send();
      } catch (IOException $e) {
        throw  ($e);
      }
      
      return $res;
    }
   
    /**
     * Retrieve the answer
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage
     */
    public function retrieve($response) {

      // HACK: Read statuscode, so all headers are read before $response
      // is dumped. Otherwise the result is b0rked.
      $response->getStatusCode();
      $this->cat && $this->cat->debug('<<<', $response);
      
      try {
        $xml= '';
        while ($buf= $response->readData()) $xml.= $buf;
        
        $this->cat && $this->cat->debug('<<<', $xml);
        if ($answer= SOAPMessage::fromString($xml)) {

          // Check encoding
          if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
            @list($type, $charset)= explode('; charset=', $content_type);
            if (!empty($charset)) $answer->setEncoding($charset);
          }

          $answer->action= $this->action;
        }
      } catch (XPException $e) {
        throw ($e);
      }
      
      // Fault?
      if (NULL !== ($fault= $answer->getFault())) {
        throw (new SOAPFaultException($fault));
      }
      
      // HTTP_OK return code?
      if (200 != $response->getStatusCode()) {
        throw (new Exception('Unexpected return code: '.$response->getStatusCode()));
      }
      
      return $answer;
    }
  }
?>
