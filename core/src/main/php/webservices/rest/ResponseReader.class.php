<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A Response reader
   */
  class ResponseReader extends Object {
    protected $deserializer;
    protected $marshalling;

    /**
     * Creates a new instance
     *
     * @param webservices.rest.RestDeserializer $deserializer
     * @param webservices.rest.RestMarshalling $marshalling
     */
    public function __construct($deserializer, $marshalling) {
      $this->deserializer= $deserializer;
      $this->marshalling= $marshalling;
    }

    /**
     * Reads the value
     *
     * @param  lang.Type $t
     * @param  io.streams.InputStream $is
     * @return var
     */
    public function read(Type $t, InputStream $is) {
      return $this->marshalling->unmarshal($t, $this->deserializer->deserialize($is, Type::$VAR));
    }
  }
?>