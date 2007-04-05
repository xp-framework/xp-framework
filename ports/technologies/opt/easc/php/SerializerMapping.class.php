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
     * @param   string serialized
     * @param   &int length
     * @param   array<string, mixed> context default array()
     * @return  &mixed
     */
    function &valueOf($serialized, &$length, $context= array()) { }
  
  }
?>
