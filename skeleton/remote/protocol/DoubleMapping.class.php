<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.Double');

  /**
   * Mapping for lang.types.Double
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class DoubleMapping extends Object {

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
    function &valueOf(&$serializer, &$serialized, $context= array()) {
      // No implementation
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
      return 'd:'.$value->value.';';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &handledClass() {
      return XPClass::forName('lang.types.Double');
    }
  } implements(__FILE__, 'remote.protocol.SerializerMapping');
?>
