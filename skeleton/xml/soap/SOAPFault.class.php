<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * SOAP fault
   *
   * @purpose  XML subtree
   */
  class SOAPFault extends XML {
    var 
      $faultcode, 
      $faultstring, 
      $faultactor= NULL,
      $detail= NULL;
    
    /**
     * Fill in this fault with data
     *
     * @access  public
     * @param   string faultcode
     * @param   string faultstring
     * @param   string faultactor default NULL
     * @param   mixed detail default NULL
     */  
    function create(
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
  }
?>
