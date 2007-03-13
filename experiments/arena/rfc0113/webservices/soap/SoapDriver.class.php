<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SoapDriver extends Object {
    public
      $drivers    = array();
      
    protected static
      $instance   = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function __construct() {
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
        self::$instance= new SoapDriver();
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
