<?php
/* This class is part of the XP framework
 *
 * $Id: WddxHttpTransport.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::wddx::transport;

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
      $this->_conn= new peer::http::HttpConnection($url);
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
      
      if (!is('webservices.wddx.WddxMessage', $message)) throw(new lang::IllegalArgumentException(
        'parameter "message" must be a webservices.wddx.WddxMessage'
      ));
      
      // Send request
      $this->_conn->request->setMethod(HTTP_POST);
      $this->_conn->request->setParameters(new peer::http::RequestData(
        $message->getDeclaration()."\n".
        $message->getSource(0)
      ));
      
      $this->_conn->request->setHeader('Content-Type', 'text/xml; charset='.$message->getEncoding());
      $this->_conn->request->setHeader('User-Agent', 'XP Framework WDDX Client (http://xp-framework.net)');

      // Add custom headers
      $this->_conn->request->addHeaders($this->_headers);
      
      try {
        $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
        $res= $this->_conn->request->send($this->_conn->getTimeout());
      } catch (io::IOException $e) {
        throw ($e);
      }
      
      return $res;
    }
    
    /**
     * Retrieve a WDDX message.
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @return  webservices.wddx.WddxMessage
     */
    public function retrieve($response) {
      $this->cat && $this->cat->debug('<<<', $response->toString());
      
      try {
        $code= $response->getStatusCode();
      } catch (peer::SocketException $e) {
        throw($e);
      }
      
      switch ($code) {
        case HTTP_OK:
        case HTTP_INTERNAL_SERVER_ERROR:
          try {
            $xml= '';
            while ($buf= $response->readData()) $xml.= $buf;

            $this->cat && $this->cat->debug('<<<', $xml);
            if ($answer= webservices::wddx::WddxMessage::fromString($xml)) {

              // Check encoding
              if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
                @list($type, $charset)= explode('; charset=', $content_type);
                if (!empty($charset)) $answer->setEncoding($charset);
              }
            }
          } catch (::Exception $e) {
            throw($e);
          }

          // Fault?
          if (NULL !== ($fault= $answer->getFault())) {
            throw(new ($fault));
          }
          
          return $answer;
        
        case HTTP_AUTHORIZATION_REQUIRED:
          throw(new lang::IllegalAccessException(
            'Authorization required: '.$response->getHeader('WWW-Authenticate')
          ));
        
        default:
          throw(new lang::IllegalStateException(
            'Unexpected return code: '.$response->getStatusCode()
          ));
      }
    }    
  }
?>
