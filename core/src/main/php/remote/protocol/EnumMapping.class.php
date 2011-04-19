<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Enum', 'remote.protocol.SerializerMapping');

  /**
   * Mapping for Enums
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class EnumMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
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
      $className= $value->getClassName();
      $memberName= $value->name();

      return sprintf(
        'O:%d:"%s":1:{s:4:"name";%s}',
        strlen($className),
        $className,
        $serializer->representationOf($value->name(), $context)
      );      
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return XPClass::forName('lang.Enum');
    }
  } 
?>
