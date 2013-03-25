<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.StreamingOutput',
    'io.streams.MemoryInputStream'
  );
  
  /**
   * Test response class
   *
   * @see  xp://webservices.rest.srv.StreamingOutput
   */
  class StreamingOutputTest extends TestCase {

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function no_input_stream() {
      $this->assertEquals(NULL, create(new StreamingOutput())->inputStream);
    }

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function input_stream_given() {
      $s= new MemoryInputStream('Test');
      $this->assertEquals($s, create(new StreamingOutput($s))->inputStream);
    }
  }
?>
