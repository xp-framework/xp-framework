<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
    
    
//  uses();
  
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SoapDriver extends Object {
    public
      $drivers    = array(),
      $usedriver  = 'SOAPXP';
      
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
     * @param   string fqcn, boolean supportsWsdl
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
      return $this->drivers;
    }

    /**
     * Select Drivers
     *
     * @param   string driver
     * @throws  lang.IllegalArgumentException
     */
    public function selectDriver($driver) {
      if (!isset($this->drivers[$driver])) {
        throw new IllegalArgumentException('Driver '.$driver.' is not a valid Driver');
      }
      $this->usedriver= $driver;
    }

    /**
     * Create an instance of a SoapDriver in WSDL-Mode.
     *
     * @param   string endpoint, string uri
     * @return  object 
     */
    public function fromWsdl($endpoint, $uri) {
      // Find first driver that supports WSDL
      if ($this->drivers[$this->usedriver]['wsdl']) {
        $client= XPClass::forName($this->drivers[$this->usedriver]['fqcn'])->newInstance($endpoint);
        $client->setWsdl(TRUE);
        return $client;
      }
      
      foreach ($this->drivers as $driver) {
        if ($driver['wsdl']) {
          $client= XPClass::forName($driver['fqcn'])->newInstance($endpoint);
          $client->setWsdl(TRUE);
          return $client;
        }
      }
      
      throw new IllegalStateException('No SOAP driver registered with WSDL abilities');
    }

    /**
     * Create an instance of a SoapDriver in WSDL-Mode.
     *
     * @param   string endpoint, string uri
     * @return  object
     */
    public function fromEndpoint($endpoint, $uri) {
      var_dump($this->drivers[$this->usedriver]['fqcn']);
      return XPClass::forName($this->drivers[$this->usedriver]['fqcn'])->newInstance($endpoint, $uri);        
      
    }
  }
?>
