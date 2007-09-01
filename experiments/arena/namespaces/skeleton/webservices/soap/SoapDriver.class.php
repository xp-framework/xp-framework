<?php
/* This class is part of the XP framework
 *
 * $Id: SoapDriver.class.php 10331 2007-05-10 13:59:24Z rene $ 
 */

  namespace webservices::soap;
    
  /**
   * This is the SOAP driver factory that should be used
   * to retrieve a SOAP client.
   *
   *
   * Example:
   *  <code>
   *    $client= SoapDriver::getInstance()->forEndpoint($endpoint, 'urn:SomeThing', XP); 
   *
   *    $client->registerMapping(
   *      new QName('http://namespace', 'SoapValue'),
   *      XPClass::forName('you.wanna.map.to.this.class')
   *    );
   *    
   *    $result= $client->invoke(
   *      'someSoapFunction',
   *      new Parameter('foo', new SoapLong($foo))
   *    );
   *  </code>
   * @test      xp://webservices.soap.SoapDriverTest
   * @see       php://soap
   * @purpose   SOAP driver factory
   */
  class SoapDriver extends lang::Object {
    public
      $drivers    = array();
    
    const
      XP          = 'SOAPXP',
      NATIVE      = 'SOAPNATIVE';
      
    protected static
      $instance   = NULL;
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->drivers[self::XP]= array(
        'fqcn'  => 'webservices.soap.xp.XPSoapClient',
        'wsdl'  => FALSE
      );
      
      if (extension_loaded('soap')) {
        $this->drivers[self::NATIVE]= array(
          'fqcn'  => 'webservices.soap.native.NativeSoapClient',
          'wsdl'  => TRUE
        );
      }
    }
    
    /**
     * Retrieve an instance of the class
     *
     * @return  object self::$instance
     */
    public static function getInstance() {
      if (NULL === self::$instance) {
        self::$instance= new self();
      }
      
      return self::$instance;
    }

    /**
     * Registers a new SoapDriver. The new driver must have the 
     * same contructor and 
     *
     * @param   string fqcn
     * @param   bool supportsWsdl
     * @return  string
     */
    public function registerDriver($fqcn, $supportsWsdl) {
      static $nr= 0;
      
      $this->drivers['SOAPRUNTIME'.$nr]= array(
        'fqcn'  => $fqcn,
        'wsdl'  => $supportsWsdl
      );
      
      return 'SOAPRUNTIME'.$nr++;
    }
    
    /**
     * Shows available, registred drivers
     *
     * @return  drivers[]
     */
    public function availableDrivers() {
      return array_keys($this->drivers);
    }

    /**
     * Create an instance of a SoapDriver in WSDL-mode.
     *
     * @param   string endpoint
     * @param   string preferred default NULL
     * @return  lang.Object
     */
    public function forWsdl($endpoint, $preferred= ) {
      $s= lang::XPClass::forName($this->drivers[$this->driverName($preferred, TRUE)]['fqcn'])->newInstance($endpoint, '');
      $s->setWsdl($endpoint);
      return $s;
    }

    /**
     * Create an instance of a SoapDriver in non-WSDL-mode.
     *
     * @param   string endpoint
     * @param   string uri
     * @param   string preferred default NULL
     * @return  lang.Object
     */
    public function forEndpoint($endpoint, $uri, $preferred= ) {
      return lang::XPClass::forName($this->drivers[$this->driverName($preferred)]['fqcn'])->newInstance($endpoint, $uri);        
    }
    
    /**
     * Fetch driver with given name and requested capabilities.
     *
     * @param   string preferred
     * @param   bool wsdl default FALSE
     * @return  string
     * @throws  lang.IllegalStateException if no driver with requested capabilities could be found
     */
    public function driverName($preferred, $wsdl= ) {
      if (
        isset($this->drivers[$preferred]) &&
        (!$wsdl || $this->drivers[$preferred]['wsdl'])
      ) {
        return $preferred;
      }
      
      foreach ($this->drivers as $name => $cap) {
        if ($wsdl && !$cap['wsdl']) continue;
        
        return $name;
      }
      
      throw new lang::IllegalStateException('No SOAP driver registered with WSDL abilities');
    }
  }
?>
