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
   * @see      xp://xml.soap.SOAPNode
   * @purpose  Base class for all SOAP types
   */
  class SoapType extends Object {
  
    /**
     * Return a string representation for use in SOAP
     *
     * @access  public
     * @return  string 
     */
    public function toString() { 
      return FALSE; 
    }
    
    /**
     * Returns this type's name
     *
     * @access  public
     * @return  string
     */
    public function getType() { 
      return FALSE; 
    }

    /**
     * Returns this type's name or FALSE if there's no 
     * special name
     *
     * @access  public
     * @return  string
     */    
    public function getItemName() { 
      return FALSE; 
    }
  }
?>
