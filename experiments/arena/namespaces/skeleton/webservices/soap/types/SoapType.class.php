<?php
/* This class is part of the XP framework
 *
 * $Id: SoapType.class.php 10196 2007-05-04 10:56:54Z kiesel $ 
 */

  namespace webservices::soap::types;

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
  class SoapType extends lang::Object {
  
    /**
     * Return a string representation for use in SOAP
     *
     * @return  string 
     */
    public function toString() { 
      return FALSE; 
    }
    
    /**
     * Returns this type's name
     *
     * @return  string
     */
    public function getType() { 
      return FALSE; 
    }

    /**
     * Returns this type's name or FALSE if there's no 
     * special name
     *
     * @return  string
     */    
    public function getItemName() { 
      return FALSE; 
    }
  }
?>
