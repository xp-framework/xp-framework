<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.Payload');

  /**
   * Abstract base class for serialization. Serializers are responsible for
   * creating the output format representation such as XML or JSON from a 
   * given payload.
   *
   * @see   xp://webservices.rest.RestJsonSerializer
   * @see   xp://webservices.rest.RestXmlSerializer
   */
  abstract class RestSerializer extends Object {

    /**
     * Return the Content-Type header's value
     *
     * @return  string
     */
    public abstract function contentType();
    
    /**
     * Serialize
     *
     * @param   var value
     * @return  string
     */
    public abstract function serialize($payload);
    
  }
?>
