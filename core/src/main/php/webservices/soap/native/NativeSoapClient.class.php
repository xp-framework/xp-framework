<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.URL',
    'xml.QName',
    'util.log.Traceable',
    'webservices.soap.ISoapClient',
    'webservices.soap.native.NativeSoapTypeMapper',
    'webservices.soap.CommonSoapFault',
    'webservices.soap.SOAPFaultException'
  );

  /**
   * Wrapper for the PHP5 soap extension.
   * 
   * @see      php://soap
   * @purpose  Integration of the PHP5 soap extension into the XP framework
   */
  class NativeSoapClient extends Object implements ISoapClient, Traceable {
    protected
      $endpoint = '',
      $uri      = '',
      $wsdl     = FALSE,
      $cat      = NULL,
      $version  = NULL,
      $location = NULL,
      $encoding = xp::ENCODING,
      $style    = SOAP_RPC,
      $use      = SOAP_ENCODED,
      $ctimeout = NULL,
      $timeout  = NULL;
    
    protected
      $map      = array();

    /**
     * Constructor
     *
     * @param   string endpoint
     * @param   string uri default NULL
     */
    public function __construct($endpoint, $uri= NULL) {
      $this->endpoint= new URL($endpoint);
      $this->uri= $uri;
      $this->wsdl= FALSE;
      $this->map= array();
    }

    /**
     * Set connect timeout
     *
     * @param   int timeout
     */
    public function setConnectTimeout($i) {
      $this->ctimeout= $i;
    }

    /**
     * Set timeout
     *
     * @param   int timeout
     */
    public function setTimeout($i) {
      $this->timeout= $i;
    }

    /**
     * Get connect timeout
     *
     * @return  int
     */
    public function getConnectTimeout() {
      return $this->ctimeout;
    }

    /**
     * Set timeout
     *
     * @return  int
     */
    public function getTimeout() {
      return $this->timeout;
    }

    /**
     * Sets the soap version
     * SOAP_1_1 and SOAP_1_2 are supported
     *
     * @param   int version
     */
    public function setSoapVersion($version) {
      $this->version= $version;
    }

    /**
     * Set location url of the soap service
     *
     * @deprecated  Use setEndpoint() instead
     * @param   string location
     */
    public function setLocation($location) {
      $this->setEndpoint($location);
    }

    /**
     * Set endpoint url of soap service
     *
     * @param string  url
     */
    public function setEndpoint($url) {
      $this->location= $url;
    }

    /**
     * Set encoding
     *
     * @deprecated  Use setEncoding()
     * @param   string charset
     */
    public function setCharset($charset) {
      $this->setEncoding($charset);
    }

    /**
     * Get encoding
     *
     * @deprecated  Use getEncoding()
     * @return  string
     */
    public function getCharset() {
      return $this->getEncoding();
    }

    /**
     * Set encoding
     *
     * @param string  encoding
     */
    public function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get encoding
     * 
     * @return  string
     */
    public function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set Style, can be one of SOAP_RPC (default), 
     * SOAP_DOCUMENT.
     *
     * @param   int style
     */
    public function setStyle($style) {
      $this->style= $style;
    }

    /**
     * Get Style
     *
     * @return  int
     */
    public function getStyle() {
      return $this->style;
    }

    /**
     * Set Encoding, can be one of SOAP_ENCODED (default),
     * SOAP_LITERAL
     *
     * @param   int encoding
     */
    public function setSoapEncoding($encoding) {
      $this->use= $encoding;
    }

    /**
     * Get Encoding
     *
     * @return  int
     */
    public function getSoapEncoding() {
      return $this->use;
    }

    /**
     * Set trace 
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
    
    /**
     * Turns WSDL mode on or off
     *
     * @param   bool usewsdl
     */
    public function setWsdl($usewsdl) {
      $this->wsdl= $usewsdl;
    }

    /**
     * Registers a class map
     *
     * @param   xml.QName object
     * @param   lang.XPClass class
     */
    public function registerMapping(QName $qname, XPClass $class) {
      $this->map[$qname->localpart]= xp::reflect($class->getName());
    }

    /**
     * Iterate over all arguments to wrap them into ext/soap
     * value objects, if needed
     *
     * @param   var[]
     * @return  var[]
     */
    protected function checkParams($args) {
      $type= new NativeSoapTypeMapper();

      foreach ($args as $i => $a) {
        if ($type->supports($a)) {
          $args[$i]= $type->box($a);
        }
      }
      
      return $args;
    }

    /**
     * Invoke method call
     *
     * @param   string method name
     * @param   var vars
     * @return  var answer
     * @throws  webservices.soap.SOAPFaultException
     */
    public function invoke() {
      $args= func_get_args();
      $method= array_shift($args);
      
      $options= array(
        'encoding'    => $this->getEncoding(),
        'exceptions'  => 0,
        'trace'       => ($this->cat != NULL),
        'user_agent'  => 'XP-Framework/'.get_class($this)
      );

      if (NULL !== $this->ctimeout) {
        $options['connection_timeout']= $this->ctimeout;
      }

      if (NULL !== $this->timeout) {
        // NOOP
      }

      if (NULL !== $this->endpoint->getUser()) {
        $options['login']= $this->endpoint->getUser();
      }
      
      if (NULL !== $this->endpoint->getPassword()) {
        $options['password']= $this->endpoint->getPassword();
      }
      
      if (sizeof($this->map)) {
        $options['classmap']= $this->map;
      }
      
      $this->version && $options['soap_version']= $this->version;
      $this->location && $options['location']= $this->location;

      if ($this->wsdl) {
        $client= new SoapClient($this->endpoint->getURL(), $options);
      } else {

        // Do not overwrite location if already set from outside
        isset($options['location']) || $options['location']= $this->endpoint->getURL();

        // Assert we have a uri
        if (!$this->uri) throw new IllegalArgumentException (
          'SOAP uri required in non-wsdl mode.'
        );

        $options['uri']= $this->uri;
        $options['style']= $this->getStyle();
        $options['use']= $this->getSoapEncoding();
        
        $client= new SoapClient(NULL, $options);
      }

      // Take care of wrapping XP SOAP types into respective ext/soap value objects
      $result= $client->__soapCall($method, $this->checkParams($args));
      
      $this->cat && $this->cat->debug('>>>',
        $client->__getLastRequestHeaders(),
        $client->__getLastRequest()
      );
      $this->cat && $this->cat->debug('<<<', 
        $client->__getLastResponseHeaders(),
        $client->__getLastResponse()
      );
      
      if (is_soap_fault($result)) throw new SOAPFaultException(
        new CommonSoapFault($result->faultcode, $result->faultstring)
      );
      
      return $result;
    }
  }
?>
