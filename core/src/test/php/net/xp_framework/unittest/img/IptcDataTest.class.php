<?php namespace net\xp_framework\unittest\img;

use img\util\IptcData;


/**
 * TestCase for IptcData class
 *
 * @see      xp://net.xp_framework.unittest.img.MetaDataTest
 * @see      xp://img.util.IptcData
 * @purpose  Unittest
 */
class IptcDataTest extends MetaDataTest {

  /**
   * Extract from file and return the instance
   *
   * @param   io.File f
   * @return  lang.Generic the instance
   */
  protected function extractFromFile(\io\File $f) {
    return IptcData::fromFile($f);
  }

  /**
   * Test default value is returned if no IPTC data is found
   *
   */
  #[@test]
  public function defaultValueIfNotFound() {
    $this->assertNull(IptcData::fromFile($this->resourceAsFile('exif-only.jpg'), null));
  }

  /**
   * Test empty IPTC data
   *
   */
  #[@test]
  public function emptyIptcData() {
    $this->assertEquals('', IptcData::$EMPTY->getTitle());
  }

  /**
   * Test reading IPTC data from a file which contains exif-data
   * only
   *
   */
  #[@test, @expect('lang.ElementNotFoundException')]
  public function fromFileWithoutIptc() {
    $this->extractFromFile($this->resourceAsFile('exif-only.jpg'));
  }

  /**
   * Test reading IPTC data from a file which contains exif-data
   * AND iptc-data
   *
   */
  #[@test]
  public function fromFileWithExifAndIptc() {
    $i= $this->extractFromFile($this->resourceAsFile('exif-and-iptc.jpg'));
    $this->assertEquals('Unittest Image', $i->getTitle());
  }

  /**
   * Test reading IPTC data from a file which contains exif-data
   * AND iptc-data
   *
   */
  #[@test]
  public function fromFile() {
    $i= $this->extractFromFile($this->resourceAsFile('iptc-only.jpg'));
    $this->assertEquals('Unittest Image', $i->getTitle());
  }
}
