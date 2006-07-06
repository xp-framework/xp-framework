<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for serializer mappings
   *
   * @purpose  Interface
   */
  class SerializerMapping extends Interface {

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
    function &valueOf(&$serializer, $serialized, &$length, $context= array()) { }
  
  }
?>
