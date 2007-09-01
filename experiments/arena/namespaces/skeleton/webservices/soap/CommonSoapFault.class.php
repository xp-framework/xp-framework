<?php
/* This class is part of the XP framework
 *
 * $Id: CommonSoapFault.class.php 10397 2007-05-18 13:52:42Z kiesel $ 
 */

  namespace webservices::soap;
  
  uses('xml.XML');

  /**
   * SOAP fault
   *
   * @purpose  XML subtree
   */
  class CommonSoapFault extends xml::XML {
    public 
      $faultcode    = '', 
      $faultstring  = '', 
      $faultactor   = NULL,
      $detail       = NULL;

    /**
     * Constructor
     *
     * @param   string faultcode
     * @param   string faultstring
     * @param   string faultactor default NULL
     * @param   mixed detail default NULL
     */  
    public function __construct(
      $faultcode, 
      $faultstring, 
      $faultactor= , 
      $detail= 
    ) {
      $this->faultcode= $faultcode;
      $this->faultstring= $faultstring;
      $this->faultactor= $faultactor;
      $this->detail= $detail;
    }
    
    /**
     * Set Faultcode
     *
     * @param   string faultcode
     */
    public function setFaultcode($faultcode) {
      $this->faultcode= $faultcode;
    }

    /**
     * Get Faultcode
     *
     * @return  string
     */
    public function getFaultcode() {
      return $this->faultcode;
    }

    /**
     * Set Faultstring
     *
     * @param   string faultstring
     */
    public function setFaultstring($faultstring) {
      $this->faultstring= $faultstring;
    }

    /**
     * Get Faultstring
     *
     * @return  string
     */
    public function getFaultstring() {
      return $this->faultstring;
    }

    /**
     * Set Faultactor
     *
     * @param   string faultactor
     */
    public function setFaultactor($faultactor) {
      $this->faultactor= $faultactor;
    }

    /**
     * Get Faultactor
     *
     * @return  string
     */
    public function getFaultactor() {
      return $this->faultactor;
    }

    /**
     * Set Detail
     *
     * @param   mixed detail
     */
    public function setDetail($detail) {
      $this->detail= $detail;
    }

    /**
     * Get Detail
     *
     * @return  mixed
     */
    public function getDetail() {
      return $this->detail;
    }
  }
?>
