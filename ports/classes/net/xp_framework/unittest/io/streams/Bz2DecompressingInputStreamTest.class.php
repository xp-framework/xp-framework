<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.io.streams.AbstractDecompressingInputStreamTest',
    'io.streams.Bz2DecompressingInputStream'
  );

  /**
   * TestCase
   *
   * @ext      bz2
   * @see      xp://io.streams.Bz2DecompressingInputStream
   */
  class Bz2DecompressingInputStreamTest extends AbstractDecompressingInputStreamTest {

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
     * @param   io.streams.InputStream wrapped
     * @return  int level
     * @return  io.streams.InputStream
     */
    protected function newStream(InputStream $wrapped) {
      return new Bz2DecompressingInputStream($wrapped);
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
