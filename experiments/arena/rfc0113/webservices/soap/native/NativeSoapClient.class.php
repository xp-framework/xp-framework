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
      $trace    = NULL;
    
    /**
     * Constructor
     *
     * @param   peer.URL endpoint
     * @param   string uri
     */
    public function __construct($endpoint, $uri) {
      $this->endpoint= $endpoint;
      $this->uri= $uri;
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
      $this->map[$qname->toString()]= $class->getName();
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
        'location'    => $this->endpoint->getURL(),
        'uri'         => $this->uri,
        'encoding'    => 'iso-8859-1',
        'use'         => SOAP_RPC,
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
      
      $client= new SoapClient(NULL, $options);
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
