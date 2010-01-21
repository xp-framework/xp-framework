<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.io.archive.ZipFileTest');

  /**
   * TestCase
   *
   */
  abstract class ZipFileVendorTest extends ZipFileTest {
    protected $vendor= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->vendor= $this->vendorName();
    }
    
    /**
     * Returns vendor name
     *
     * @return  string
     */
    protected abstract function vendorName();
    
    /**
     * Tests reading an empty zipfile
     *
     */
    #[@test]
    public function emptyZipFile() {
      $this->assertEquals(array(), $this->entriesIn($this->archiveReaderFor($this->vendor, 'empty')));
    }

    /**
     * Tests reading an zipfile with one entry called "hello.txt" in its 
     * root directory.
     *
     */
    #[@test]
    public function helloZip() {
      $entries= $this->entriesIn($this->archiveReaderFor($this->vendor, 'hello'));
      $this->assertEquals(1, sizeof($entries));
      $this->assertEquals('hello.txt', $entries[0]->getName());
      $this->assertEquals(5, $entries[0]->getSize());
      $this->assertFalse($entries[0]->isDirectory());
    }
  }
?>
