<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A serializer
   *
   * @see   xp://webservices.rest.RestRequest#setPayload
   */
  interface RestSerializer {

    /**
     * Return the Content-Type header's value
     *
     * @return  string
     */
    public function contentType();
    
    /**
     * Serialize
     *
     * @param   var value
     * @return  string
     */
    public function serialize($payload);
    
  }
?>
