<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.log.Traceable',
    'webservices.soap.SOAPFaultException',
    'webservices.soap.SOAPFault'    
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class NativeSoapClient extends Object implements Traceable {
    public
      $endpoint = '',
      $uri      = '',
      $wsdl     = FALSE,
      $cat      = NULL;
    
    /**
     * Constructor
     *
     * @param   peer.URL endpoint
     * @param   string uri
     */
    public function __construct($endpoint, $uri, $useWsdl= FALSE) {
      $this->endpoint= $endpoint;
      $this->uri= $uri;
      $this->wsdl= $useWsdl;
      $this->map= array(
        'SOAPLong'  => 'long'
      );
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function registerMapping($qname, $class) {
      $this->map[$qname->localpart]= $class->getName();
    }
    
    /**
     * Iterate over all arguments to wrap them into ext/soap
     * value objects, if needed
     *
     * @param   mixed[]
     * @return  mixed[]
     */
    private function checkParams($args) {
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
     * @param   mixed parameter
     * @return  mixed
     * @throws  lang.IllegalArgumentException if parameter type cannot be converted
     */
    private function wrapParameter($parameter) {
    
      // Instanceof testing frenzy begins here.
      // This is necessary to convert XP Parameter and SOAP*-Types to 
      // Soap-ext SoapParam and SoapVar
      switch (TRUE) {
        case ($parameter instanceof Parameter):
          if ($parameter->value instanceof SOAPType) {
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
     * @param   mixed vars
     * @return  mixed answer
     * @throws  lang.IllegalArgumentException
     * @throws  webservices.soap.SOAPFaultException
     */
    public function invoke() {
      $args= func_get_args();
      $method= array_shift($args);
      
      $options= array(
        'encoding'    => 'iso-8859-1',
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
      
      if ($this->wsdl) {
        $client= new SoapClient($this->endpoint->getURL(), $options);
      } else {
      
        $options['location']= $this->endpoint->getURL();
        $options['uri']= $this->uri;
        $options['use']= SOAP_RPC;
        
        $client= new SoapClient(NULL, $options);

        // Take care of wrapping XP SOAP types into respective ext/soap value objects
        $args= $this->checkParams($args);
      }
      
      $result= call_user_func_array(array($client, $method), $args);
      
      $this->cat && $this->cat->debug('>>>',
        $client->__getLastRequestHeaders(),
        $client->__getLastRequest()
      );
      $this->cat && $this->cat->debug('<<<', 
        $client->__getLastResponseHeaders(),
        $client->__getLastResponse()
      );
      
      if (is_soap_fault($result)) throw new SoapFaultException(
        new SOAPFault($result->faultcode, $result->faultstring)
      );
      
      return $result;
    }
  }
?>
