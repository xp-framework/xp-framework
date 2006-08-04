<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates a SOAP fault occured
   *
   * @purpose  Exception
   */
  class SOAPFaultException extends XPException {
    public 
      $fault= NULL; 

    /**
     * Constructor
     *
     * @access  public
     * @param   &xml.soap.SOAPFault fault
     */
    public function __construct(&$fault) {
      $this->fault= $fault;
      parent::__construct($this->fault->faultstring);
    }

    /**
     * Get Fault
     *
     * @access  public
     * @return  &xml.soap.SOAPFault
     */
    public function &getFault() {
      return $this->fault;
    }
    
    /**
     * Return a string representation of this exception
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString().sprintf(
        "  [\n    fault.faultcode= '%s'\n    fault.faultactor= '%s'\n    fault.detail= %s\n  ]\n",
        $this->fault->faultcode,
        $this->fault->faultactor,
        var_export($this->fault->detail, 1)
      );
    }
  }
?>
