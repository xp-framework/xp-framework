<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.rpc.transport.AbstractRpcTransport',
    'scriptlet.rpc.RpcFaultException',
    'webservices.json.rpc.JsonResponseMessage',
    'peer.http.HttpConnection'
  );

  /**
   * Transport for JSON RPC requests over HTTP.
   *
   * @purpose  HTTP Transport for RPC clients
   */
  class JsonRpcHttpTransport extends AbstractRpcTransport {
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
     * Send RPC message
     *
     * @param   scriptlet.rpc.AbstractRpcMessage message
     * @return  scriptlet.HttpScriptletResponse
     */
    public function send(JsonMessage $message) {
      with ($request= $this->_conn->create(new HttpRequest())); {
        $request->setMethod(HTTP_POST);
        $request->setParameters(new RequestData($message->serializeData()));
        $request->setHeader('Content-Type', $message->getContentType().'; charset='.$message->getEncoding());
        $request->setHeader('User-Agent', 'XP Framework Client (http://xp-framework.net)');

        // Add custom headers
        $request->addHeaders($this->_headers);

        $this->cat && $this->cat->debug('>>>', $request->getRequestString());
        return $this->_conn->send($request);
      }
    }
    
    /**
     * Retrieve a RPC message.
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @return  scriptlet.rpc.AbstractRpcMessage
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
          $answer= JsonResponseMessage::fromString($xml);
          if ($answer) {

            // Check encoding
            if (NULL !== ($content_type= $response->getHeader('Content-Type'))) {
              @list($type, $charset)= explode('; charset=', $content_type);
              if (!empty($charset)) $answer->setEncoding($charset);
            }
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
