<?php
/* This class is part of the XP framework
 *
 * $Id: SOAPHTTPTransport.class.php 10015 2007-04-16 16:36:48Z kiesel $ 
 */

  namespace webservices::soap::transport;

  uses(
    'webservices.soap.transport.SOAPTransport',
    'webservices.soap.SOAPFaultException',
    'peer.http.HttpConnection'
  );
  
  // Different modes for SOAP-Action announcement (you can use NULL to obey any SOAPAction header)
  define('SOAP_ACTION_COMPUTE',       0x0001);
  define('SOAP_ACTION_HARDCODE',      0x0002);
  define('SOAP_ACTION_EMPTY',         0x0003);
  define('SOAP_ACTION_NULL',          0x0004);
  
  /**
   * HTTP transport. Also handles HTTPS.
   *
   * @ext       openssl
   * @purpose   Transport SOAP messages
   * @see       xp://webservices.soap.SOAPClient
   */
  class SOAPHTTPTransport extends SOAPTransport {
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
    public function __construct($url, $headers= array(), $actiontype= ) {
      $this->_conn= new peer::http::HttpConnection($url);
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
      return sprintf('%s { %s }', $this->getClassName(), $this->_conn->request->url->_info['url']);
    }

    /**
     * Send the message
     *
     * @param   webservices.soap.xp.XPSoapMessage message
     * @return  peer.http.HttpResponse
     * @throws  lang.IllegalArgumentException in case the given parameter is not a webservices.soap.SOAPMessage
     */
    public function send(webservices::soap::xp::XPSoapMessage $message) {
      if (!$this->_conn->request) throw(new lang::IllegalArgumentException(
        'Factory method failed'
      ));

      // Action
      $this->action= $message->action;

      // Post XML
      $this->_conn->request->setMethod(HTTP_POST);
      $this->_conn->request->setParameters(new peer::http::RequestData(
        $message->getDeclaration()."\n".
        $message->getSource(0)
      ));
      
      switch ($this->_actiontype) {
        case SOAP_ACTION_COMPUTE:
          $this->_conn->request->setHeader('SOAPAction', '"'.$message->action.'#'.$message->method.'"');
          break;
        
        case SOAP_ACTION_HARDCODE:
          $this->_conn->request->setHeader('SOAPAction', '"'.$message->action.'"');
          break;
        
        case SOAP_ACTION_EMPTY:
          $this->_conn->request->setHeader('SOAPAction', '""');
          break;
        
        case SOAP_ACTION_NULL:
          $this->_conn->request->setHeader('SOAPAction', '');
          break;
        
        default:
      }
      
      $this->_conn->request->setHeader('Content-Type', 'text/xml; charset='.$message->getEncoding());

      // Add more headers
      $this->_conn->request->addHeaders($this->_headers);
      $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
      return $this->_conn->request->send($this->_conn->getTimeout());
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
        case HTTP_OK:
        case HTTP_INTERNAL_SERVER_ERROR:
          $xml= '';
          while ($buf= $response->readData()) $xml.= $buf;

          $this->cat && $this->cat->debug('<<<', $xml);
          if ($answer= webservices::soap::xp::XPSoapMessage::fromString($xml)) {

            // Check encoding
            if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
              preg_match('/^([^;]+)(; ?charset=([^;]+))?/i', $content_type, $matches);
              $type= $matches[1];
              if (!empty($matches[3])) $answer->setEncoding($matches[3]);
            }

            $answer->action= $this->action;
          }

          // Fault?
          if (NULL !== ($fault= $answer->getFault())) {
            throw new webservices::soap::SOAPFaultException($fault);
          }
          
          return $answer;
        
        case HTTP_AUTHORIZATION_REQUIRED:
          throw new lang::IllegalAccessException(
            'Authorization required: '.$response->getHeader('WWW-Authenticate')
          );
        
        default:
          throw new lang::IllegalStateException('Unexpected return code: '.$response->getStatusCode());
      }
    }
  }
?>
