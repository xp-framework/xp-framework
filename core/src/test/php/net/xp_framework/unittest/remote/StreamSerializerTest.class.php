<?php namespace net\xp_framework\unittest\remote;

use remote\protocol\Serializer;
use remote\protocol\SerializedStream;

/**
 * Unit test for Serializer class working with SerializedStream
 *
 * @see  xp://remote.Serializer
 * @see  xp://remote.SerializedStream
 */
class StreamSerializerTest extends SerializerTest {

  /**
   * Unserializes a value from a given serialized representation
   *
   * @param  string $bytes
   * @param  lang.Type $t
   * @return var value
   */
  protected function unserialize($bytes, $t= null, $ctx= array()) {
    $stream= new SerializedStream(new \io\streams\MemoryInputStream($bytes), $chunk= 8);
    return $this->serializer->valueOf($stream, $ctx);
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
