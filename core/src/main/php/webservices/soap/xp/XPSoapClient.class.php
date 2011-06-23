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
    public 
      $encoding           = 'iso-8859-1',
      $transport          = NULL,
      $action             = '',
      $targetNamespace    = NULL,
      $mapping            = NULL,
      $headers            = array();
    
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
     * Set endpoint url for soap service
     *
     * @param   string url
     */
    public function setEndpoint($url) {
      $this->transport= new SOAPHTTPTransport($url);
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
     * @param   string url
     * @throws  lang.MethodNotImplementedException
     */
    public function setWsdl($url) {
      throw new MethodNotImplementedException('XPSoapClient does not support WSDL-Mode');
    }

    public function setStyle($style) {
      throw new MethodNotImplementedException('XPSoapClient only supports soap style rpc');
    }

    public function getStyle() {
      return SOAP_RPC;
    }

    public function setSoapEncoding($encoding) {
      throw new MethodNotImplementedException('XPSoapClient only supports soap encoding "encoded"');
    }

    public function getSoapEncoding() {
      return SOAP_ENCODED;
    }
    
    /**
     * Dummy function to set the soap version, which is not supported
     * by the XPSoap-client.
     *
     * @param   int version
     * @throws  lang.MethodNotImplementedException
     */
    public function setSoapVersion($version) {
      throw new MethodNotImplementedException('XPSoapClient cannot change the soap version');
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
