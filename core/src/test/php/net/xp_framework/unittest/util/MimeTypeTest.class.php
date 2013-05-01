<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('unittest.TestCase', 'util.MimeType');

  /**
   * Test MimeType class
   *
   * @see  xp://util.MimeType
   */
  class MimeTypeTest extends TestCase {
    
    /**
     * Tests getByFilename()
     */
    #[@test]
    public function text_file() {
      $this->assertEquals('text/plain', MimeType::getByFilename('test.txt'));
    }

    /**
     * Tests getByFilename()
     */
    #[@test]
    public function html_file() {
      $this->assertEquals('text/html', MimeType::getByFilename('test.html'));
    }

    /**
     * Tests getByFilename()
     */
    #[@test]
    public function uppercase_extension() {
      $this->assertEquals('text/html', MimeType::getByFilename('test.HTML'));
    }
  }
?>
