<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
    
    
//  uses();
  
  define('SOAPNATIVE',  'webservices.soap.native.NativeSoapClient');
  define('SOAPXP',      'webservices.soap.xp.XPSoapClient');

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
      $usedriver  = SOAPXP;
      
    protected static
      $instance   = NULL;
      
    static function __static() {
      self::$instance= new self();
    }
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->drivers= array(
        'SOAPXP'  => array(
          'fqcn'  => 'webservices.soap.xp.XpSoapClient',
          'wsdl'  => FALSE
        )
      );
      
      // $this->drivers['webservices.soap.xp.XpSoapClient']= TRUE;
      if (extension_loaded('soap')) {
        $this->drivers['SOAPNATIVE']= array(
          'fqcn'  => 'webservices.soap.native.NativeSoapClient',
          'wsdl'  => TRUE
        );
        
        // $this->drivers['webservices.soap.native.NativeSoapClient']= 'wsdl';
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function getInstance() {
      return self::$instance;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function availableDrivers() {
      return $this->drivers;
    }

    /**
     * Select Drivers
     *
     * @param   
     * @throws  lang.IllegalArgumentException
     */
    public function selectDriver($driver) {
      if (!isset($this->drivers[$driver])) {
        throw new IllegalArgumentException('Driver '.$driver.' is not a valid Driver');
      }
      $this->usedriver= $driver;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function fromEndpoint($endpoint, $uri) {
    if ($this->usedriver == SOAPNATIVE) {}
      return XPClass::forName(SOAPNATIVE)->newInstance($endpoint, $uri);        
      return XPClass::forName(SOAPXP)->newInstance($endpoint, $uri);
    }
  }
?>
