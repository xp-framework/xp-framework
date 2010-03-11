<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.rpc.transport.AbstractRpcTransport',
    'webservices.soap.SOAPFaultException',
    'peer.http.HttpConnection',
    'peer.http.HttpConstants'
  );
  
  // Different modes for SOAP-Action announcement (you can use NULL to obey any SOAPAction header)
  // Deprecated
  define('SOAP_ACTION_COMPUTE',       0x0001);
  define('SOAP_ACTION_HARDCODE',      0x0002);
  define('SOAP_ACTION_EMPTY',         0x0003);
  define('SOAP_ACTION_NULL',          0x0004);
  
  /**
   * HTTP transport. Also handles HTTPS.
   *
   * @purpose   Transport SOAP messages
   * @see       xp://webservices.soap.SOAPClient
   */
  class SOAPHTTPTransport extends AbstractRpcTransport {
    const
      ACTION_COMPUTE    = 0x0001,
      ACTION_HARDCODE   = 0x0002,
      ACTION_EMPTY      = 0x0003,
      ACTION_NULL       = 0x0004;

    public
      $_conn        = NULL,
      $_action      = '',
      $_actiontype  = NULL,
      $_headers     = array();
      
    /**
     * Constructor
     *
     * @param   string url
     * @param   array headers default array()
     * @param   int actiontype
     */  
    public function __construct($url, $headers= array(), $actiontype= self::ACTION_COMPUTE) {
      $this->_conn= new HttpConnection($url);
      $this->_headers= array_merge(
        array('User-Agent' => 'XP-Framework SOAP Client (http://xp-framework.net)'),
        $headers
      );
      $this->_actiontype= $actiontype;
    }
    
    /**
     * Set the timeout for the request.
     * Note: this is the read-timeout.
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      $this->_conn->setTimeout($timeout);
    }
    
    /**
     * Adds a header. If this header is already set, it will
     * be overwritten.
     *
     * Example:
     * <code>
     *   $transport->setHeader('X-Binford', '6100 (more power)');
     * </code>
     *
     * @param   string name header name
     * @param   string value header value
     */
    public function setHeader($name, $value) {
      $this->_headers[$name]= $value;
    }

    /**
     * Retrieve the current timeout setting.
     * Note: this is the read-timeout.
     *
     * @return  int
     */
    public function getTimeout() {
      return $this->_conn->getTimeout();
    }

    /**
     * Destructor
     *
     */
    public function __destruct() {
      delete($this->_conn);
    }
    
    /**
     * Create a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.xp::stringOf($this->_conn).')';
    }

    /**
     * Send the message
     *
     * @param   webservices.soap.xp.XPSoapMessage message
     * @return  peer.http.HttpResponse
     * @throws  lang.IllegalArgumentException in case the given parameter is not a webservices.soap.SOAPMessage
     */
    public function send($message) {
      if (!$message instanceof XPSoapMessage) {
        throw new IllegalArgumentException(__METHOD__.' expects webservices.soap.xp.XPSoapMessage, but got '.xp::typeOf($message));
      }
      $headers= $this->_headers;

      // Action
      $this->_action= $message->action;

      switch ($this->_actiontype) {
        case self::ACTION_COMPUTE:
          $headers['SOAPAction']= '"'.$message->action.'#'.$message->method.'"';
          break;
        
        case self::ACTION_HARDCODE:
          $headers['SOAPAction']= '"'.$message->action.'"';
          break;
        
        case self::ACTION_EMPTY:
          $headers['SOAPAction']= '""';
          break;
        
        case self::ACTION_NULL:
          $headers['SOAPAction']= '';
          break;
        
        default:
      }
      
      $headers['Content-Type']= 'text/xml; charset='.$message->getEncoding();

      // Post XML
      with ($request= $this->_conn->create(new HttpRequest())); {
        $request->setMethod(HttpConstants::POST);
        $request->setParameters(new RequestData(
          $message->getDeclaration()."\n".
          $message->getSource(0)
        ));
        $request->addHeaders($headers);
        
        $this->cat && $this->cat->debug('>>>', $request->getRequestString());
        return $this->_conn->send($request);
      }
    }
   
    /**
     * Retrieve the answer
     *
     * @param   peer.http.HttpResponse response
     * @return  webservices.soap.SOAPMessage
     * @throws  io.IOException in case the data cannot be read
     * @throws  xml.XMLFormatException in case the XML is not well-formed
     * @throws  lang.IllegalAccessException in case authorization is required
     * @throws  lang.IllegalStateException in case an unexpected HTTP status code is returned
     */
    public function retrieve($response) {
      $this->cat && $this->cat->debug('<<<', $response->toString());
      $code= $response->getStatusCode();
      
      switch ($code) {
        case HttpConstants::STATUS_OK:
        case HttpConstants::STATUS_INTERNAL_SERVER_ERROR:
          $xml= '';
          while ($buf= $response->readData()) $xml.= $buf;

          $this->cat && $this->cat->debug('<<<', $xml);
          $answer= XPSoapMessage::fromString($xml);
          $answer->action= $this->_action;

          // Fault?
          if (NULL !== ($fault= $answer->getFault())) {
            throw new SOAPFaultException($fault);
          }
          
          return $answer;
        
        case HttpConstants::STATUS_AUTHORIZATION_REQUIRED:
          throw new IllegalAccessException(
            'Authorization required: '.$response->getHeader('WWW-Authenticate')
          );
        
        default:
          throw new IllegalStateException('Unexpected return code: '.$response->getStatusCode());
      }
    }
  }
?>
