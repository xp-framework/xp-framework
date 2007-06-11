<?php
/* This class is part of the XP framework
 *
 * $Id: GenericHttpTransport.class.php 7540 2006-08-04 15:23:14Z kiesel $ 
 */

  uses('peer.http.HttpConnection', 'scriptlet.rpc.RpcFaultException');

  /**
   * Transport for generic RPC requests over HTTP.
   *
   * @purpose  HTTP Transport for RPC clients
   */
  class GenericHttpTransport extends Object {
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
     * Sets the message class which will retrieve the answer
     *
     * @param   lang.XPClass c
     */
    public function setMessageClass($c) {
      $this->messageClass= $c;
    }    

    /**
     * Send RPC message
     *
     * @param   scriptlet.rpc.AbstractRpcMessage message
     * @return  scriptlet.HttpScriptletResponse
     */
    public function send($message) {
      
      if (!is('scriptlet.rpc.AbstractRpcMessage', $message)) throw(new IllegalArgumentException(
        'parameter "message" must be a scriptlet.rpc.AbstractRpcMessage'
      ));
      
      // Send XML
      $this->_conn->request->setMethod(HTTP_POST);
      $this->_conn->request->setParameters(new RequestData($message->serializeData()));
      $this->_conn->request->setHeader('Content-Type', $message->getContentType().'; charset='.$message->getEncoding());
      $this->_conn->request->setHeader('User-Agent', 'XP Framework Client (http://xp-framework.net)');

      // Add custom headers
      $this->_conn->request->addHeaders($this->_headers);
      
      try {
        $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
        $res= $this->_conn->request->send($this->_conn->getTimeout());
      } catch (IOException $e) {
        throw ($e);
      }
      
      return $res;
    }
    
    /**
     * Retrieve a RPC message.
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @return  scriptlet.rpc.AbstractRpcMessage
     */
    public function retrieve($response) {
      $this->cat && $this->cat->debug('<<<', $response->toString());
      
      try {
        $code= $response->getStatusCode();
      } catch (SocketException $e) {
        throw($e);
      }
      
      switch ($code) {
        case HTTP_OK:
        case HTTP_INTERNAL_SERVER_ERROR:
          try {
            $xml= '';
            while ($buf= $response->readData()) $xml.= $buf;

            $this->cat && $this->cat->debug('<<<', $xml);
            $m= $this->messageClass->getMethod('fromString');
            $answer= $m->invoke(NULL, array($xml));
            if ($answer) {

              // Check encoding
              if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
                @list($type, $charset)= explode('; charset=', $content_type);
                if (!empty($charset)) $answer->setEncoding($charset);
              }
            }
          } catch (Exception $e) {
            throw($e);
          }

          // Fault?
          if (NULL !== ($fault= $answer->getFault())) {
            throw(new RpcFaultException($fault));
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
