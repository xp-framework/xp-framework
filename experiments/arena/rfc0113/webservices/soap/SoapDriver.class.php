<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
    
  /**
   * (Insert class' description here)
   *
   * @test      xp://webservices.soap.SoapDriverTest
   * @ext       extension
   * @see       reference
   * @purpose   purpose
   */
  class SoapDriver extends Object {
    public
      $drivers    = array();
    
    const
      XP          = 'SOAPXP',
      NATIVE      = 'SOAPNATIVE';
      
    protected static
      $instance   = NULL;
      
    static function __static() {
      self::$instance= new self();
    }
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->drivers['SOAPXP']= array(
        'fqcn'  => 'webservices.soap.xp.XPSoapClient',
        'wsdl'  => FALSE
      );
      
      if (extension_loaded('soap')) {
        $this->drivers['SOAPNATIVE']= array(
          'fqcn'  => 'webservices.soap.native.NativeSoapClient',
          'wsdl'  => TRUE
        );
      }
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
     * Gets an instance of the class
     *
     * @return  object self::$instance
     */
    public static function getInstance() {
      return self::$instance;
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
     * Create an instance of a SoapDriver in WSDL-Mode.
     *
     * @param   string endpoint, string uri
     * @return  object 
     */
    public function fromWsdl($endpoint, $preferred= NULL) {
      return XPClass::forName($this->drivers[$this->driverName($preferred, TRUE)]['fqcn'])->newInstance($endpoint, '', TRUE);
    }

    /**
     * Create an instance of a SoapDriver in WSDL-Mode.
     *
     * @param   string endpoint, string uri
     * @return  object
     */
    public function fromEndpoint($endpoint, $uri, $preferred= NULL) {
      return XPClass::forName($this->drivers[$this->driverName($preferred)]['fqcn'])->newInstance($endpoint, $uri);        
    }
    
    /**
     * Fetch driver with given name and requested capabilities.
     *
     * @param   string preferred
     * @param   bool wsdl default FALSE
     * @return  string
     * @throws  lang.IllegalStateException if no driver with requested capabilities could be found
     */
    public function driverName($preferred, $wsdl= FALSE) {
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
      
      throw new IllegalStateException('No SOAP driver registered with WSDL abilities');
    }
  }
?>
