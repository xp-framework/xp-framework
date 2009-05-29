<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.rpc.transport.AbstractRpcTransport',
    'webservices.wddx.WddxFaultException',
    'webservices.wddx.WddxMessage',
    'peer.http.HttpConnection'
  );

  /**
   * Transport for Wddx requests over HTTP.
   *
   * @see      xp://webservices.wddx.WddxClient
   * @purpose  HTTP Transport
   */
  class WddxHttpTransport extends AbstractRpcTransport {
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
    public function send(WddxMessage $message) {
      
      // Send request
      with ($r= $this->_conn->create(new HttpRequest())); {
        $r->setMethod(HTTP_POST);
        $r->setParameters(new RequestData(
          $message->getDeclaration()."\n".
          $message->getSource(0)
        ));

        $r->setHeader('Content-Type', 'text/xml; charset='.$message->getEncoding());
        $r->setHeader('User-Agent', 'XP Framework WDDX Client (http://xp-framework.net)');

        // Add custom headers
        $r->addHeaders($this->_headers);

        $this->cat && $this->cat->debug('>>>', $r->getRequestString());
        return $this->_conn->send($r);
      }
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
            throw new WddxFaultException($fault);
          }
          
          return $answer;
        
        case HTTP_AUTHORIZATION_REQUIRED:
          throw(new IllegalAccessException(
            'Authorization required: '.$response->getHeader('WWW-Authenticate')
          ));
        
        default:
          throw new IllegalStateException(
            'Unexpected return code: '.$response->getStatusCode()
          );
      }
    }    
  }
?>
