<?php namespace net\xp_framework\unittest\io\streams;

use io\streams\InflatingInputStream;


/**
 * TestCase
 *
 * @ext      zlib
 * @see      xp://io.streams.InflatingInputStream
 */
class InflatingInputStreamTest extends AbstractDecompressingInputStreamTest {

  /**
   * Get extension we depend on
   *
   * @return  string
   */
  protected function extension() {
    return 'zlib';
  }

  /**
   * Get stream
   *
   * @param   io.streams.InputStream wrapped
   * @return  int level
   * @return  io.streams.InputStream
   */
  protected function newStream(\io\streams\InputStream $wrapped) {
    return new InflatingInputStream($wrapped);
  }

  /**
   * Compress data
   *
   * @param   string in
   * @return  int level
   * @return  string
   */
  protected function compress($in, $level) {
    return gzdeflate($in, $level);
  }
}
