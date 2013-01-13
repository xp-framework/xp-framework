<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'io.Stream', 'io.FileUtil', 'io.streams.Streams', 'io.streams.MemoryInputStream');

  /**
   * TestCase
   *
   * @see      xp://io.FileUtil
   */
  class FileUtilTest extends TestCase {

    /**
     * Test getContents() method
     *
     */
    #[@test]
    public function get_contents() {
      $data= 'Test';
      $f= new Stream();
      $f->open(STREAM_MODE_WRITE);
      $f->write($data);
      $f->close();

      $this->assertEquals($data, FileUtil::getContents($f));
    }

    /**
     * Test setContents() method
     *
     */
    #[@test]
    public function set_contents() {
      $data= 'Test';
      $f= new Stream();
      $this->assertEquals(strlen($data), FileUtil::setContents($f, $data), 'bytes written equals');
      $this->assertEquals($data, FileUtil::getContents($f));
    }

    /**
     * Test getContents() method
     *
     * @see   https://github.com/xp-framework/xp-framework/pull/220
     */
    #[@test]
    public function get_contents_read_returns_less_than_size() {
      $data= 'Test';
      $f= newinstance('io.Stream', array(), '{
        public function read($size= 4096) { return parent::read(min(1, $size)); }
      }');
      $f->open(STREAM_MODE_WRITE);
      $f->write($data);
      $f->close();

      $this->assertEquals($data, FileUtil::getContents($f));
    }
  }
?>
