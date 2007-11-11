<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.img.MetaDataTest', 'img.util.ExifData');

  /**
   * TestCase for IptcData class
   *
   * @see      xp://net.xp_framework.unittest.img.MetaDataTest
   * @see      xp://img.util.ExifData
   * @purpose  Unittest
   */
  class ExifDataTest extends MetaDataTest {

    /**
     * Sets up this unittest 
     *
     * @throws  unittest.PrerequisitesNotMetError
     */
    public function setUp() {
      if (!extension_loaded('exif')) {
        throw new PrerequisitesNotMetError('EXIF extension not loaded');
      }
    }

    /**
     * Extract from file and return the instance
     *
     * @param   io.File f
     * @return  lang.Generic the instance
     */
    protected function extractFromFile(File $f) {
      return ExifData::fromFile($f);
    }

    /**
     * Test default value is returned if no Exif data is found
     *
     */
    #[@test]
    public function defaultValueIfNotFound() {
      $this->assertNull(ExifData::fromFile($this->resourceAsFile('iptc-only.jpg'), NULL));
    }

    /**
     * Test empty EXIF data
     *
     */
    #[@test]
    public function emptyExifData() {
      $this->assertEquals(0, ExifData::$EMPTY->getWidth());
    }
  
    /**
     * Test reading Exif data from a file which contains exif-data
     * only
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function fromFileWithoutExif() {
      $this->extractFromFile($this->resourceAsFile('iptc-only.jpg'));
    }

    /**
     * Test reading Exif data from a file which contains exif-data
     * AND Exif-data
     *
     */
    #[@test]
    public function fromFileWithExifAndIptc() {
      $i= $this->extractFromFile($this->resourceAsFile('exif-and-iptc.jpg'));
      $this->assertEquals(1, $i->getWidth());
      $this->assertEquals(1, $i->getHeight());
    }

    /**
     * Test reading Exif data from a file which contains exif-data
     * AND Exif-data
     *
     */
    #[@test]
    public function fromFile() {
      $i= $this->extractFromFile($this->resourceAsFile('exif-only.jpg'));
      $this->assertEquals(1, $i->getWidth());
      $this->assertEquals(1, $i->getHeight());
    }
  }
?>
