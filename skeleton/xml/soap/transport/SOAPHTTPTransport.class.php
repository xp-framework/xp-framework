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
   * @see       xp://xml.soap.SOAPClient
   */
  class SOAPHTTPTransport extends SOAPTransport {
    var
      $_conn        = NULL,
      $_action      = '',
      $_actiontype  = NULL,
      $_headers     = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string url
     * @param   array headers default array()
     * @param   int actiontype
     */  
    function __construct($url, $headers= array(), $actiontype= SOAP_ACTION_COMPUTE) {
      $this->_conn= &new HttpConnection($url);
      $this->_headers= array_merge(
        array('User-Agent' => 'XP-Framework SOAP Client (http://xp-framework.net)'),
        $headers
      );
      $this->_actiontype= $actiontype;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      delete($this->_conn);
      parent::__destruct();
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
      $this->cat && $this->cat->debug('<<<', $response->toString());
      
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
