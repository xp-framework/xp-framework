<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.ArrayList');

  /**
   * Mapping for strictly numeric arrays
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class ArrayListMapping extends Object {

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
      $a= &new ArrayList();
      $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
      $offset= strlen($size)+ 2+ 2;
      for ($i= 0; $i < $size; $i++) {
        $a->values[$i]= &$serializer->valueOf(substr($serialized, $offset), $len, $context);
        $offset+= $len;
      }
      $length= $offset+ 1;
      return $a;
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
      $s= 'A:'.sizeof($value->values).':{';
      foreach (array_keys($value->values) as $key) {
        $s.= $serializer->representationOf($value->values[$key], $context);
      }
      return $s.'}';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &handledClass() {
      return XPClass::forName('lang.types.ArrayList');
    }
  } implements(__FILE__, 'remote.protocol.SerializerMapping');
?>
