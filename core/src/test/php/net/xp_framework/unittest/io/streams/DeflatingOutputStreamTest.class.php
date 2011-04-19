<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.io.streams.AbstractCompressingOutputStreamTest',
    'io.streams.DeflatingOutputStream'
  );

  /**
   * TestCase
   *
   * @ext      zlib
   * @see      xp://io.streams.DeflatingOutputStream
   */
  class DeflatingOutputStreamTest extends AbstractCompressingOutputStreamTest {

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
     * @param   io.streams.OutputStream wrapped
     * @return  int level
     * @return  io.streams.OutputStream
     */
    protected function newStream(OutputStream $wrapped, $level) {
      return new DeflatingOutputStream($wrapped, $level);
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
?>
