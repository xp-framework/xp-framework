<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.ArrayList', 'remote.protocol.SerializerMapping');

  /**
   * Mapping for strictly numeric arrays
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class ArrayListMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   array<string, mixed> context default array()
     * @return  mixed
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      $a= ArrayList::newInstance($serialized->consumeSize());
      
      $serialized->offset++;  // Opening "{"
      for ($i= 0; $i < $a->length; $i++) {
        $a[$i]= $serializer->valueOf($serialized, $context);
      }
      $serialized->offset++;  // Closing "}"
      return $a;
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @param   server.protocol.Serializer serializer
     * @param   lang.Object value
     * @param   array<string, mixed> context default array()
     * @return  string
     */
    public function representationOf($serializer, $value, $context= array()) {
      $s= 'A:'.$value->length.':{';
      for ($i= 0; $i < $value->length; $i++) {
        $s.= $serializer->representationOf($value[$i], $context);
      }
      return $s.'}';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return XPClass::forName('lang.types.ArrayList');
    }
  } 
?>
