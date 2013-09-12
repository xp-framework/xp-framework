<?php namespace net\xp_framework\unittest\io\archive\vendors;

use net\xp_framework\unittest\io\archive\ZipFileTest;


/**
 * TestCase
 *
 */
abstract class ZipFileVendorTest extends ZipFileTest {
  protected $vendor= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    parent::setUp();
    $this->vendor= $this->vendorName();

    // Check whether any PHP extensions are required by a specific test,
    // and skip if this extension is not loaded. This is done by checking
    // for an @ext annotation.
    $m= $this->getClass()->getMethod($this->name);
    if ($m->hasAnnotation('ext') && !\lang\Runtime::getInstance()->extensionAvailable($ext= $m->getAnnotation('ext'))) {
      throw new \unittest\PrerequisitesNotMetError('Extension not available', null, array($ext));
    }
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
   * Tests reading a zipfile with one entry called "hello.txt" in its 
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

  /**
   * Tests reading an zipfile with one entry called "הצü.txt" in its 
   * root directory.
   *
   */
  #[@test]
  public function umlautZip() {
    $entries= $this->entriesIn($this->archiveReaderFor($this->vendor, 'umlaut'));
    $this->assertEquals(1, sizeof($entries));
    $this->assertEquals('הצü.txt', $entries[0]->getName());
    $this->assertEquals(0, $entries[0]->getSize());
    $this->assertFalse($entries[0]->isDirectory());
  }
}
