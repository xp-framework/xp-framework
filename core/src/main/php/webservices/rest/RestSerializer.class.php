<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.Payload');

  /**
   * A serializer
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestSerializerConversionTest
   * @see   xp://webservices.rest.RestRequest#setPayload
   */
  abstract class RestSerializer extends Object {

    /**
     * Convert data
     *
     * @param  var $data
     * @throws lang.IllegalStateException
     * @deprecated This has been moved to the new marshaller API
     */
    public function convert($data) {
      throw new IllegalStateException('RestSerializer::convert() is deprecated');
    }

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
