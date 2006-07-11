<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Mapping for util.Date
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class DateMapping extends Object {

    /**
     * Returns a value for the given serialized string
     *
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   string serialized
     * @param   &int length
     * @param   array<string, mixed> context default array()
     * @return  &mixed
     */
    function &valueOf(&$serializer, $serialized, &$length, $context= array()) {
      $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
      $length= strlen($v)+ 3;
      $value= &new Date((int)$v);
      return $value;
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   &lang.Object value
     * @param   array<string, mixed> context default array()
     * @return  string
     */
    function representationOf(&$serializer, &$value, $context= array()) {
      return 'T:'.$value->getTime().';';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &handledClass() {
      return XPClass::forName('util.Date');
    }
  } implements(__FILE__, 'remote.protocol.SerializerMapping');
?>
