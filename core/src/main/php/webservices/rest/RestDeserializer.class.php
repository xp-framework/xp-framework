<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.Payload');

  /**
   * Deserializer abstract base class
   */
  abstract class RestDeserializer extends Object {

    /**
     * Convert data
     *
     * @param  var $data
     * @throws lang.IllegalStateException
     * @deprecated This has been moved to the new marshaller API
     */
    public function convert($data) {
      throw new IllegalStateException('RestDeserializer::convert() is deprecated');
    }

    /**
     * Deserialize
     *
     * @param   io.streams.InputStream in
     * @return  var
     * @throws  lang.FormatException
     */
    public abstract function deserialize($in);
  }
?>
