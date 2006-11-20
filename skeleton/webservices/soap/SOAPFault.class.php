<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses('xml.XML');

  /**
   * SOAP fault
   *
   * @purpose  XML subtree
   */
  class SOAPFault extends XML {
    var 
      $faultcode    = '', 
      $faultstring  = '', 
      $faultactor   = NULL,
      $detail       = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string faultcode
     * @param   string faultstring
     * @param   string faultactor default NULL
     * @param   mixed detail default NULL
     */  
    function __construct(
      $faultcode, 
      $faultstring, 
      $faultactor= NULL, 
      $detail= NULL
    ) {
      $this->faultcode= $faultcode;
      $this->faultstring= $faultstring;
      $this->faultactor= $faultactor;
      $this->detail= $detail;
    }
    
    /**
     * Set Faultcode
     *
     * @access  public
     * @param   string faultcode
     */
    function setFaultcode($faultcode) {
      $this->faultcode= $faultcode;
    }

    /**
     * Get Faultcode
     *
     * @access  public
     * @return  string
     */
    function getFaultcode() {
      return $this->faultcode;
    }

    /**
     * Set Faultstring
     *
     * @access  public
     * @param   string faultstring
     */
    function setFaultstring($faultstring) {
      $this->faultstring= $faultstring;
    }

    /**
     * Get Faultstring
     *
     * @access  public
     * @return  string
     */
    function getFaultstring() {
      return $this->faultstring;
    }

    /**
     * Set Faultactor
     *
     * @access  public
     * @param   string faultactor
     */
    function setFaultactor($faultactor) {
      $this->faultactor= $faultactor;
    }

    /**
     * Get Faultactor
     *
     * @access  public
     * @return  string
     */
    function getFaultactor() {
      return $this->faultactor;
    }

    /**
     * Set Detail
     *
     * @access  public
     * @param   mixed detail
     */
    function setDetail($detail) {
      $this->detail= $detail;
    }

    /**
     * Get Detail
     *
     * @access  public
     * @return  mixed
     */
    function getDetail() {
      return $this->detail;
    }
  }
?>
