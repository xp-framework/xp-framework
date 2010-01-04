<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.StreamTransfer',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.StreamTransfer
   */
  class StreamTransferTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function dataTransferred() {
      $out= new MemoryOutputStream();

      $s= new StreamTransfer(new MemoryInputStream('Hello'), $out);
      $s->transferAll();

      $this->assertEquals('Hello', $out->getBytes());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nothingAvailableAfterTransfer() {
      $in= new MemoryInputStream('Hello');

      $s= new StreamTransfer($in, new MemoryOutputStream());
      $s->transferAll();

      $this->assertEquals(0, $in->available());
    }
  }
?>
