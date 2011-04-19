<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'remote.protocol.SerializerMapping');

  /**
   * Mapping for util.Date
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class DateMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   [:var] context default array()
     * @return  var
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      $value= new Date((int)$serialized->consumeWord());
      return $value;
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
      return 'T:'.$value->getTime().';';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return XPClass::forName('util.Date');
    }
  } 
?>
