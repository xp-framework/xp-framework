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
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->drivers[]= 'webservices.soap.xp.XpSoapClient';
      if (extension_loaded('soap')) {
        $this->drivers[]= 'webservices.soap.native.NativeSoapClient';
      }
    }
          
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function getInstance() {
      if (NULL === self::$instance) {
        self::$instance= new self();
      }
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function driverAvailable($driver) {
      // TBI
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function fromWsdl($endpoint, $uri) {
      return XPClass::forName(SOAPNATIVE)->newInstance($endpoint, $uri, TRUE);
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function fromEndpoint($endpoint, $uri) {
    
      // use the XP SoapClient if ext-soap is not loaded or 
      // SOAPXP is the selected driver. 
      if (!extension_loaded('soap') || $usedriver = SOAPXP) {
        return XPClass::forName(SOAPXP)->newInstance($endpoint, $uri);
      } else {
        return XPClass::forName(SOAPNATIVE)->newInstance($endpoint, $uri, FALSE);
      }
    }

    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function instanciate($preferredOrder= array()) {
      // TBI
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function forName($url, $uri) {
      return new NativeSoapClient($url, $uri);
    }
  }
?>
