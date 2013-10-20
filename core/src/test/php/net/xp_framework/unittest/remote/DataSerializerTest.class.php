<?php namespace net\xp_framework\unittest\remote;

use remote\protocol\Serializer;
use remote\protocol\SerializedData;

/**
 * Unit test for Serializer class working with SerializedData
 *
 * @see  xp://remote.Serializer
 * @see  xp://remote.SerializedData
 */
class DataSerializerTest extends SerializerTest {

  /**
   * Unserializes a value from a given serialized representation
   *
   * @param  string $bytes
   * @param  lang.Type $t
   * @return var value
   */
  protected function unserialize($bytes, $t= null, $ctx= array()) {
    return $this->serializer->valueOf(new SerializedData($bytes), $ctx);
  }

  /**
   * Serializes a value and returns a serialized representation
   *
   * @param  var $value
   * @return string bytes
   */
  protected function serialize($value) {
    return $this->serializer->representationOf($value);
  }
}
