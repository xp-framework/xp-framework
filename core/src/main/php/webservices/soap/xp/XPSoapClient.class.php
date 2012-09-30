<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.QName',
    'util.log.Traceable',
    'webservices.soap.ISoapClient',
    'webservices.soap.xp.XPSoapMessage',
    'webservices.soap.xp.XPSoapMapping',
    'webservices.soap.transport.SOAPHTTPTransport'
  );
  
  /**
   * Basic SOAP-Client
   *
   * @see      xp://webservices.soap.SoapDriver
   * @test     xp://net.xp_framework.unittest.soap.SoapClientTest
   * @purpose  Generic SOAP client base class
   */
  class XPSoapClient extends Object implements ISoapClient, Traceable {
    protected
      $transport          = NULL,
      $encoding           = xp::ENCODING,
      $action             = '',
      $targetNamespace    = NULL,
      $mapping            = NULL,
      $headers            = array();

    static function __static() {

      // Define constants used here which will be missing if ext/soap
      // has not been loaded
      if (!defined('SOAP_RPC')) {
        define('SOAP_RPC',        0x01);
        define('SOAP_DOCUMENT',   0x02);
        define('SOAP_ENCODED',    0x01);
        define('SOAP_LITERAL',    0x02);
        define('SOAP_1_1',        0x01);
        define('SOAP_1_2',        0x02);
      }
    }
    
    /**
     * Constructor
     *
     * @param   string url
     * @param   string action Action
     * @param   string targetNamespace default NULL
     */
    public function __construct($url, $action) {
      $this->setEndpoint($url);
      $this->action= $action;
      $this->targetNamespace= NULL;
      $this->mapping= new XPSoapMapping();
    }

    /**
     * Retrieve members directly; this method is supposed to keep BC for
     * the class' behaviour until the next minor release (5.9).
     *
     * @param   string key
     * @return  mixed
     */
    public function __get($key) {
      if (in_array($key, array('transport', 'encoding', 'action', 'targetNamespace', 'mapping', 'headers'))) {
        trigger_error('Direct use of XPSoapClient member "'.$key.'" is discouraged.', E_USER_DEPRECATED);
        return $this->{$key};
      }

      return NULL;
    }

    /**
     * Magic __set method - prohibits any attempt to set member directly.
     *
     * @param   mixed key
     * @param   mixed value
     */
    public function __set($key, $value) {
      throw new IllegalAccessException('Direct modification of member "'.$key.'" is no longer supported.');
    }

    /**
     * Set transport
     *
     * @param webservices.soap.transport.SOAPHTTPTransport transport
     */
    public function setTransport(SOAPHTTPTransport $transport) {
      $this->transport= $transport;
    }

    /**
     * Set connect timeout
     *
     * @param   int timeout
     */
    public function setConnectTimeout($i) {
      $this->getTransport()->setConnectTimeout($i);
    }

    /**
     * Set timeout
     *
     * @param   int timeout
     */
    public function setTimeout($i) {
      $this->getTransport()->setTimeout($i);
    }

    /**
     * Get connect timeout
     *
     * @return  int
     */
    public function getConnectTimeout() {
      return $this->getTransport()->getConnectTimeout();
    }

    /**
     * Set timeout
     *
     * @return  int
     */
    public function getTimeout() {
      return $this->getTransport()->getTimeout();
    }

    /**
     * Set endpoint url for soap service
     *
     * @param   string url
     */
    public function setEndpoint($url) {
      $this->transport= new SOAPHTTPTransport($url);
    }

    /**
     * Retrieve transport implementation
     *
     * @return  webservices.soap.transport.SOAPHTTPTransport
     */
    public function getTransport() {
      return $this->transport;
    }

    /**
     * Set TargetNamespace
     *
     * @param   string targetNamespace
     */
    public function setTargetNamespace($targetNamespace= NULL) {
      $this->targetNamespace= $targetNamespace;
    }

    /**
     * Set encoding
     *
     * @param   string encoding either utf-8 oder iso-8859-1
     */
    public function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Retrieve encoding
     *
     * @return  string
     */
    public function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->transport->setTrace($cat);
    }

    /**
     * Dummy function to set WSDL-mode, which is not supported
     * by the XPSoap-client.
     *
     * @param   bool enabled
     * @throws  lang.MethodNotImplementedException
     */
    public function setWsdl($enabled) {
      throw new MethodNotImplementedException('XPSoapClient does not support WSDL-Mode');
    }

    /**
     * Set Soap style; this implementation only supports SOAP_RPC
     *
     * @param   int style
     */
    public function setStyle($style) {
      switch ($style) {
        case SOAP_RPC: return;
        default:
          throw new IllegalArgumentException('XPSoapClient does not support given soap style');
      }
    }

    /**
     * Retrieve current style
     *
     * @return  int
     */
    public function getStyle() {
      return SOAP_RPC;
    }

    /**
     * Set soap encoding; this implementation only supports SOAP_ENCODED
     *
     * @param   int encoding
     */
    public function setSoapEncoding($encoding) {
      switch ($encoding) {
        case SOAP_ENCODED: return;
        default:
          throw new IllegalArgumentException('XPSoapClient does not support given soap encoding');
      }
    }

    /**
     * Retrieve soap encoding
     *
     * @return  int
     */
    public function getSoapEncoding() {
      return SOAP_ENCODED;
    }
    
    /**
     * Dummy function to set the soap version, which is not supported
     * by the XPSoap-client.
     *
     * @param   int version
     * @throws  lang.IllegalArgumentException if version not supported
     */
    public function setSoapVersion($version) {
      switch ($version) {
        case SOAP_1_1: return;
        default:
          throw new IllegalArgumentException('XPSoapClient does not support given soap version');
      }
    }    
    
    /**
     * Register mapping for a qname to a class object
     *
     * @param   xml.QName qname
     * @param   lang.XPClass class
     */
    public function registerMapping(QName $qname, XPClass $class) {
      $this->mapping->registerMapping($qname, $class);
    }
    
    /**
     * Add a header
     *
     * @param   webservices.soap.xp.XPSoapHeader header
     * @return  webservices.soap.xp.XPSoapHeader the header added
     */
    public function addHeader($header) {
      $this->headers[]= $header;
      return $header;
    }
    
    /**
     * Invoke method call
     *
     * @param   string method name
     * @param   var vars
     * @return  var answer
     * @throws  lang.IllegalArgumentException
     * @throws  webservices.soap.SOAPFaultException
     */
    public function invoke() {
      if (!$this->transport instanceof SOAPHTTPTransport) throw new IllegalArgumentException(
        'Transport must be a webservices.soap.transport.SOAPHTTPTransport'
      );
      
      $args= func_get_args();
      
      $message= new XPSoapMessage();
      $message->setEncoding($this->encoding);
      $message->createCall($this->action, array_shift($args), $this->targetNamespace, $this->headers);
      $message->setMapping($this->mapping);
      $message->setData($args);

      // Send
      if (FALSE == ($response= $this->transport->send($message))) return FALSE;
      
      // Response
      if (FALSE == ($answer= $this->transport->retrieve($response))) return FALSE;
      
      $answer->setMapping($this->mapping);
      $data= $answer->getData();

      if (sizeof($data) == 1) return current($data);
      if (sizeof($data) == 0) return NULL;
      return $data;
    }
  } 
?>
