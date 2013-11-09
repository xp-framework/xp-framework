<?php namespace net\xp_framework\unittest\img;

use img\util\IptcData;

/**
 * TestCase for IptcData class
 *
 * @see  xp://net.xp_framework.unittest.img.MetaDataTest
 * @see  xp://img.util.IptcData
 */
class IptcDataTest extends MetaDataTest {

  /**
   * Extract from file and return the instance
   *
   * @param   io.File $f
   * @return  lang.Generic the instance
   */
  protected function extractFromFile(\io\File $f) {
    return IptcData::fromFile($f);
  }

  #[@test]
  public function defaultValueIfNotFound() {
    $this->assertNull(IptcData::fromFile($this->resourceAsFile('exif-only.jpg'), null));
  }

  #[@test]
  public function emptyIptcData() {
    $this->assertEquals('', IptcData::$EMPTY->getTitle());
  }

  #[@test, @expect('lang.ElementNotFoundException')]
  public function fromFileWithoutIptc() {
    $this->extractFromFile($this->resourceAsFile('exif-only.jpg'));
  }

  #[@test]
  public function fromFileWithExifAndIptc() {
    $i= $this->extractFromFile($this->resourceAsFile('exif-and-iptc.jpg'));
    $this->assertEquals('Unittest Image', $i->getTitle());
  }

  #[@test]
  public function fromFile() {
    $i= $this->extractFromFile($this->resourceAsFile('iptc-only.jpg'));
    $this->assertEquals('Unittest Image', $i->getTitle());
  }
}
