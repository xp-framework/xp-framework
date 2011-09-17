<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a SOAP type. Special SOAP types such as
   * Base64binary or DateTime are recognized by the generic
   * serializer/deserializer via is_a($arg, 'SoapType'),
   * so all user-defined types must inherit this class in
   * order for the serializer
   *
   * @see      xp://webservices.soap.xp.XPSoapNode
   * @purpose  Base class for all SOAP types
   */
  interface SoapType {
  
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */
    public function toString();
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType();

    /**
     * Returns this type's name or FALSE if there's no 
     * special name
     *
     * @return  string
     */    
    public function getItemName();

    /**
     * Retrieve type as native SOAP type
     *
     * @return  php.SoapVar
     */
    public function asSoapType();
  }
?>
