<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.URL',
    'xml.QName',
    'util.log.Traceable',
    'webservices.soap.CommonSoapFault',
    'webservices.soap.SOAPFaultException'
  );

  /**
   * Wrapper for the PHP5 soap extension.
   * 
   * @see      php://soap
   * @purpose  Integration of the PHP5 soap extension into the XP framework
   */
  class NativeSoapClient extends Object implements Traceable {
    public
      $endpoint = '',
      $uri      = '',
      $wsdl     = FALSE,
      $cat      = NULL,
      $version  = NULL,
      $location = NULL,
      $charset  = 'iso-8859-1',
      $style    = SOAP_RPC,
      $encoding = SOAP_ENCODED;
    
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
     * @param   string location
     */
    public function setLocation($location) {
      $this->location= $location;
    }

    /**
     * Set Charset
     *
     * @param   string charset
     */
    public function setCharset($charset) {
      $this->charset= $charset;
    }

    /**
     * Get Charset
     *
     * @return  string
     */
    public function getCharset() {
      return $this->charset;
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
    public function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get Encoding
     *
     * @return  int
     */
    public function getEncoding() {
      return $this->encoding;
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
      foreach ($args as $i => $a) {
        if ($a instanceof Parameter || $a instanceof SoapType) {
          $args[$i]= $this->wrapParameter($a);
        }
      }
      
      return $args;
    }

    /**
     * Wrap single argument to ext/soap value object
     *
     * @param   var parameter
     * @return  var
     * @throws  lang.IllegalArgumentException if parameter type cannot be converted
     */
    protected function wrapParameter($parameter) {

      // Instanceof testing frenzy begins here.
      // This is necessary to convert XP Parameter and SOAP*-Types to 
      // Soap-ext SoapParam and SoapVar
      switch (TRUE) {
        case ($parameter instanceof Parameter):
          if ($parameter->value instanceof SoapType) {
            return new SoapParam($this->wrapParameter($parameter->value), $parameter->name);
          }
          
          return new SoapParam($parameter->value, $parameter->name);
          
        case ($parameter instanceof SOAPLong):
          return new SoapVar($parameter->long, XSD_LONG);
          
        case ($parameter instanceof SOAPBase64Binary):
          return new SoapVar($parameter->encoded, XSD_BASE64BINARY);
          
        case ($parameter instanceof SOAPHexBinary):
          return new SoapVar($parameter->encoded, XSD_HEXBINARY);
          
        case ($parameter instanceof SOAPDateTime):
          return new SoapVar($parameter->value, XSD_DATETIME);
          
        case ($parameter instanceof SOAPHashMap):
          return $parameter->value;
          
        // case ($parameter instanceof SOAPVector):
        //   return new SoapVar($parameter->value, XSD_DATETIME);
        
        default:
          throw new IllegalArgumentException('Cannot serialize '.$parameter->getClassName());
      }
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
        'encoding'    => $this->getCharset(),
        'exceptions'  => 0,
        'trace'       => ($this->cat != NULL)
      );

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
        $options['location'] || $options['location']= $this->endpoint->getURL();

        $options['uri']= $this->uri;
        $options['style']= $this->getStyle();
        $options['use']= $this->getEncoding();
        
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
