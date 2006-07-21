<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.protocol.SerializerMapping');
  
  /**
   * Interface for serializer mappings
   *
   * @purpose  Interface
   */
  interface SerializerMapping {

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
    public function &valueOf(&$serializer, $serialized, &$length, $context= array());

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   &lang.Object value
     * @param   array<string, mixed> context default array()
     * @return  string
     */
    public function representationOf(&$serializer, &$value, $context= array());
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @access  public
     * @return  &lang.XPClass
     */
    public function &handledClass();
  }
?>
