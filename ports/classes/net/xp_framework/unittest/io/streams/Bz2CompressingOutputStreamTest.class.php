<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.io.streams.AbstractCompressingOutputStreamTest',
    'io.streams.Bz2CompressingOutputStream'
  );

  /**
   * TestCase
   *
   * @ext      bz2
   * @see      xp://io.streams.Bz2CompressingOutputStream
   */
  class Bz2CompressingOutputStreamTest extends AbstractCompressingOutputStreamTest {
  
    /**
     * Get extension we depend on
     *
     * @return  string
     */
    protected function extension() {
      return 'bz2';
    }

    /**
     * Get stream
     *
     * @param   io.streams.OutputStream wrapped
     * @return  int level
     * @return  io.streams.OutputStream
     */
    protected function newStream(OutputStream $wrapped, $level) {
      return new Bz2CompressingOutputStream($wrapped, $level);
    }

    /**
     * Compress data
     *
     * @param   string in
     * @return  int level
     * @return  string
     */
    protected function compress($in, $level) {
      return bzcompress($in, $level);
    }
  }
?>
