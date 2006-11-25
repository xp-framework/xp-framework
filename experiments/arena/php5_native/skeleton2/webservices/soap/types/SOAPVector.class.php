<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.soap.SOAPNode', 'xml.soap.types.SoapType');

  /**
   * Vector type as serialized and recogned by Apache SOAP.
   *
   * @see      xp://xml.soap.types.SoapType
   * @purpose  Vector type
   */
  class SOAPVector extends SoapType {
    public 
      $_vector;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   array params
     */
    public function __construct($params) {
      $this->_vector= &$params;
      $this->item= new SOAPNode('vec', NULL, array(
        'xmlns:vec'   => 'http://xml.apache.org/xml-soap',
        'xsi:type'    => 'vec:Vector'
      ));
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @access  public
     * @return  mixed
     */
    public function toString() {
      return $this->_vector;
    }
    
    /**
     * Returns this type's name
     *
     * @access  public
     * @return  string
     */
    public function getType() {
      return 'vec:Vector';
    }
  }
?>
