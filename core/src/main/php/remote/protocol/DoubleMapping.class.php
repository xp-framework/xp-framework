<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.Double', 'remote.protocol.SerializerMapping');

  /**
   * Mapping for lang.types.Double
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class DoubleMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   string serialized
     * @param   [:var] context default array()
     * @return  var
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      // No implementation
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @param   server.protocol.Serializer serializer
     * @param   lang.Object value
     * @param   [:var] context default array()
     * @return  string
     */
    public function representationOf($serializer, $value, $context= array()) {
      return 'd:'.$value->value.';';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return XPClass::forName('lang.types.Double');
    }
  } 
?>
