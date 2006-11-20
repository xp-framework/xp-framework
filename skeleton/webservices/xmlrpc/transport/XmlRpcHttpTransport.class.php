<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.xmlrpc.transport.XmlRpcTransport',
    'peer.http.HttpConnection'
  );

  /**
   * Transport for XmlRpc requests over HTTP.
   *
   * @see      xp://webservices.xmlrpc.XmlRpcClient
   * @purpose  HTTP Transport for XML-RPC
   */
  class XmlRpcHttpTransport extends XmlRpcTransport {
    var
      $_conn    = NULL,
      $_headers = array();
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   string url
     * @param   array headers
     */
    function __construct($url, $headers= array()) {
      $this->_conn= &new HttpConnection($url);
      $this->_headers= $headers;
    }
    
    /**
     * Create a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf('%s { %s }', $this->getClassName(), $this->_conn->request->url->_info['url']);
    }

    /**
     * Send XML-RPC message
     *
     * @access  public
     * @param   &webservices.xmlrpc.XmlRpcMessage message
     * @return  &scriptlet.HttpScriptletResponse
     */
    function &send(&$message) {
      
      if (!is('webservices.xmlrpc.XmlRpcMessage', $message)) return throw(new IllegalArgumentException(
        'parameter "message" must be a webservices.xmlrpc.XmlRpcMessage'
      ));
      
      // Send XML
      $this->_conn->request->setMethod(HTTP_POST);
      $this->_conn->request->setParameters(new RequestData($message->serializeData()));
      $this->_conn->request->setHeader('Content-Type', 'text/xml; charset='.$message->getEncoding());
      $this->_conn->request->setHeader('User-Agent', 'XP Framework XML-RPC Client (http://xp-framework.net)');

      // Add custom headers
      $this->_conn->request->addHeaders($this->_headers);
      
      try(); {
        $this->cat && $this->cat->debug('>>>', $this->_conn->request->getRequestString());
        $res= &$this->_conn->request->send($this->_conn->getTimeout());
      } if (catch('IOException', $e)) {
        return throw ($e);
      }
      
      return $res;
    }
    
    /**
     * Retrieve a XML-RPC message.
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletResponse response
     * @return  &webservices.xmlrpc.XmlRpcMessage
     */
    function &retrieve(&$response) {
      $this->cat && $this->cat->debug('<<<', $response->toString());

      try(); {
        $code= $response->getStatusCode();
      } if (catch('SocketException', $e)) {
        return throw($e);
      }
      
      switch ($code) {
        case HTTP_OK:
        case HTTP_INTERNAL_SERVER_ERROR:
          try(); {
            $xml= '';
            while ($buf= $response->readData()) $xml.= $buf;

            $this->cat && $this->cat->debug('<<<', $xml);
            if ($answer= &XmlRpcResponseMessage::fromString($xml)) {

              // Check encoding
              if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
                @list($type, $charset)= explode('; charset=', $content_type);
                if (!empty($charset)) $answer->setEncoding($charset);
              }
            }
          } if (catch('Exception', $e)) {
            return throw($e);
          }

          // Fault?
          if (NULL !== ($fault= $answer->getFault())) {
            return throw(new XmlRpcFaultException($fault));
          }
          
          return $answer;
        
        case HTTP_AUTHORIZATION_REQUIRED:
          return throw(new IllegalAccessException(
            'Authorization required: '.$response->getHeader('WWW-Authenticate')
          ));
        
        default:
          return throw(new IllegalStateException(
            'Unexpected return code: '.$response->getStatusCode()
          ));
      }
    }    
  }
?>
