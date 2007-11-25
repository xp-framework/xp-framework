<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.wddx.transport.WddxTransport',
    'webservices.wddx.WddxMessage',
    'peer.http.HttpConnection'
  );

  /**
   * Transport for Wddx requests over HTTP.
   *
   * @see      xp://webservices.wddx.WddxClient
   * @purpose  HTTP Transport
   */
  class WddxHttpTransport extends WddxTransport {
    public
      $_conn    = NULL,
      $_headers = array();
    
    /**
     * Constructor.
     *
     * @param   string url
     * @param   array headers
     */
    public function __construct($url, $headers= array()) {
      $this->_conn= new HttpConnection($url);
      $this->_headers= $headers;
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
     * Send XML-RPC message
     *
     * @param   webservices.wddx.WddxMessage message
     * @return  scriptlet.HttpScriptletResponse
     */
    public function send($message) {
      
      if (!is('webservices.wddx.WddxMessage', $message)) throw(new IllegalArgumentException(
        'parameter "message" must be a webservices.wddx.WddxMessage'
      ));
      
      // Send request
      $this->_conn->request->setMethod(HTTP_POST);
      $this->_conn->request->setParameters(new RequestData(
        $message->getDeclaration()."\n".
        $message->getSource(0)
      ));
      
      $this->_conn->request->setHeader('Content-Type', 'text/xml; charset='.$message->getEncoding());
      $this->_conn->request->setHeader('User-Agent', 'XP Framework WDDX Client (http://xp-framework.net)');

      // Add custom headers
      $this->_conn->request->addHeaders($this->_headers);
      
      $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
      return $this->_conn->request->send($this->_conn->getTimeout());
    }
    
    /**
     * Retrieve a WDDX message.
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @return  webservices.wddx.WddxMessage
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
          if ($answer= WddxMessage::fromString($xml)) {

            // Check encoding
            if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
              @list($type, $charset)= explode('; charset=', $content_type);
              if (!empty($charset)) $answer->setEncoding($charset);
            }
          }

          // Fault?
          if (NULL !== ($fault= $answer->getFault())) {
            throw(new WddxFaultException($fault));
          }
          
          return $answer;
        
        case HTTP_AUTHORIZATION_REQUIRED:
          throw(new IllegalAccessException(
            'Authorization required: '.$response->getHeader('WWW-Authenticate')
          ));
        
        default:
          throw(new IllegalStateException(
            'Unexpected return code: '.$response->getStatusCode()
          ));
      }
    }    
  }
?>
