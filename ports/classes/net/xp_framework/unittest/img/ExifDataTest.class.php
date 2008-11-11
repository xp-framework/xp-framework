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

    /**
     * Test sample image "canon-ixus.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleCanonIxus() {
      $i= $this->extractFromFile($this->resourceAsFile('canon-ixus.jpg', 'exif_org'));
      $this->assertEquals('1/350', $i->getExposureTime());
      $this->assertEquals('346/32', $i->getFocalLength());
      $this->assertEquals('Canon', $i->getMake());
      $this->assertEquals('f/4.0', $i->getApertureFNumber());
      $this->assertEquals(NULL, $i->getIsoSpeedRatings());
      $this->assertEquals(NULL, $i->getSoftware());
      $this->assertEquals(NULL, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('Canon DIGITAL IXUS', $i->getModel());
      $this->assertEquals('2001:06:09 15:17:32', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(2, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }

    /**
     * Test sample image "fujifilm-dx10.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleFujifilmDx10() {
      $i= $this->extractFromFile($this->resourceAsFile('fujifilm-dx10.jpg', 'exif_org'));
      $this->assertEquals(NULL, $i->getExposureTime());
      $this->assertEquals('58/10', $i->getFocalLength());
      $this->assertEquals('FUJIFILM', $i->getMake());
      $this->assertEquals('f/4.2', $i->getApertureFNumber());
      $this->assertEquals(150, $i->getIsoSpeedRatings());
      $this->assertEquals('Digital Camera DX-10 Ver1.00', $i->getSoftware());
      $this->assertEquals(2, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(1024, $i->getWidth());
      $this->assertEquals(768, $i->getHeight());
      $this->assertEquals('DX-10', $i->getModel());
      $this->assertEquals('2001:04:12 20:33:14', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(5, $i->getMeteringMode());
      $this->assertEquals(1, $i->getFlash());
      $this->assertTrue($i->flashUsed());
    }

    /**
     * Test sample image "fujifilm-finepix40i.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleFujifilmFinepix40i() {
      $i= $this->extractFromFile($this->resourceAsFile('fujifilm-finepix40i.jpg', 'exif_org'));
      $this->assertEquals(NULL, $i->getExposureTime());
      $this->assertEquals('870/100', $i->getFocalLength());
      $this->assertEquals('FUJIFILM', $i->getMake());
      $this->assertEquals('f/2.8', $i->getApertureFNumber());
      $this->assertEquals(200, $i->getIsoSpeedRatings());
      $this->assertEquals('Digital Camera FinePix40i Ver1.39', $i->getSoftware());
      $this->assertEquals(2, $i->getExposureProgram());
      $this->assertEquals(0, $i->getWhiteBalance());
      $this->assertEquals(600, $i->getWidth());
      $this->assertEquals(450, $i->getHeight());
      $this->assertEquals('FinePix40i', $i->getModel());
      $this->assertEquals('2000:08:04 18:22:57', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(5, $i->getMeteringMode());
      $this->assertEquals(1, $i->getFlash());
      $this->assertTrue($i->flashUsed());
    }

    /**
     * Test sample image "fujifilm-mx1700.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleFujifilmMx1700() {
      $i= $this->extractFromFile($this->resourceAsFile('fujifilm-mx1700.jpg', 'exif_org'));
      $this->assertEquals(NULL, $i->getExposureTime());
      $this->assertEquals('99/10', $i->getFocalLength());
      $this->assertEquals('FUJIFILM', $i->getMake());
      $this->assertEquals('f/7.0', $i->getApertureFNumber());
      $this->assertEquals(125, $i->getIsoSpeedRatings());
      $this->assertEquals('Digital Camera MX-1700ZOOM Ver1.00', $i->getSoftware());
      $this->assertEquals(2, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('MX-1700ZOOM', $i->getModel());
      $this->assertEquals('2000:09:02 14:30:10', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(5, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }

    /**
     * Test sample image "kodak-dc210.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleKodakDC210() {
      $i= $this->extractFromFile($this->resourceAsFile('kodak-dc210.jpg', 'exif_org'));
      $this->assertEquals('1/30', $i->getExposureTime());
      $this->assertEquals('44/10', $i->getFocalLength());
      $this->assertEquals('Eastman Kodak Company', $i->getMake());
      $this->assertEquals('f/4.0', $i->getApertureFNumber());
      $this->assertEquals(NULL, $i->getIsoSpeedRatings());
      $this->assertEquals(NULL, $i->getSoftware());
      $this->assertEquals(NULL, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('DC210 Zoom (V05.00)', $i->getModel());
      $this->assertEquals('2000:10:26 16:46:51', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(2, $i->getMeteringMode());
      $this->assertEquals(1, $i->getFlash());
      $this->assertTrue($i->flashUsed());
    }

    /**
     * Test sample image "kodak-dc240.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleKodakDC240() {
      $i= $this->extractFromFile($this->resourceAsFile('kodak-dc240.jpg', 'exif_org'));
      $this->assertEquals('1/30', $i->getExposureTime());
      $this->assertEquals('140/10', $i->getFocalLength());
      $this->assertEquals('EASTMAN KODAK COMPANY', $i->getMake());
      $this->assertEquals('f/4.0', $i->getApertureFNumber());
      $this->assertEquals(NULL, $i->getIsoSpeedRatings());
      $this->assertEquals(NULL, $i->getSoftware());
      $this->assertEquals(NULL, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('KODAK DC240 ZOOM DIGITAL CAMERA', $i->getModel());
      $this->assertEquals('1999:05:25 21:00:09', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(1, $i->getMeteringMode());
      $this->assertEquals(1, $i->getFlash());
      $this->assertTrue($i->flashUsed());
    }

    /**
     * Test sample image "nikon-e950.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleNikonE950() {
      $i= $this->extractFromFile($this->resourceAsFile('nikon-e950.jpg', 'exif_org'));
      $this->assertEquals('10/770', $i->getExposureTime());
      $this->assertEquals('128/10', $i->getFocalLength());
      $this->assertEquals('NIKON', $i->getMake());
      $this->assertEquals('f/5.5', $i->getApertureFNumber());
      $this->assertEquals(80, $i->getIsoSpeedRatings());
      $this->assertEquals('v981-79', $i->getSoftware());
      $this->assertEquals(2, $i->getExposureProgram());
      $this->assertEquals(0, $i->getWhiteBalance());
      $this->assertEquals(800, $i->getWidth());
      $this->assertEquals(600, $i->getHeight());
      $this->assertEquals('E950', $i->getModel());
      $this->assertEquals('2001:04:06 11:51:40', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(5, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }

    /**
     * Test sample image "olympus-c960.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleOlympusC960() {
      $i= $this->extractFromFile($this->resourceAsFile('olympus-c960.jpg', 'exif_org'));
      $this->assertEquals('1/345', $i->getExposureTime());
      $this->assertEquals('56/10', $i->getFocalLength());
      $this->assertEquals('OLYMPUS OPTICAL CO.,LTD', $i->getMake());
      $this->assertEquals('f/8.0', $i->getApertureFNumber());
      $this->assertEquals(125, $i->getIsoSpeedRatings());
      $this->assertEquals('OLYMPUS CAMEDIA Master', $i->getSoftware());
      $this->assertEquals(2, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('C960Z,D460Z', $i->getModel());
      $this->assertEquals('2000:11:07 10:41:43', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(5, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }

    /**
     * Test sample image "ricoh-rdc5300.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleRicohrdc5300() {
      $i= $this->extractFromFile($this->resourceAsFile('ricoh-rdc5300.jpg', 'exif_org'));
      $this->assertEquals(NULL, $i->getExposureTime());
      $this->assertEquals('133/10', $i->getFocalLength());
      $this->assertEquals('RICOH', $i->getMake());
      $this->assertEquals('f/4.0', $i->getApertureFNumber());
      $this->assertEquals(NULL, $i->getIsoSpeedRatings());
      $this->assertEquals(NULL, $i->getSoftware());
      $this->assertEquals(NULL, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(896, $i->getWidth());
      $this->assertEquals(600, $i->getHeight());
      $this->assertEquals('RDC-5300', $i->getModel());
      $this->assertEquals('2000:05:31 21:50:40', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(NULL, $i->getMeteringMode());
      $this->assertEquals(1, $i->getFlash());
      $this->assertTrue($i->flashUsed());
    }

    /**
     * Test sample image "sanyo-vpcg250.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleSanyoVpcg250() {
      $i= $this->extractFromFile($this->resourceAsFile('sanyo-vpcg250.jpg', 'exif_org'));
      $this->assertEquals('1/171', $i->getExposureTime());
      $this->assertEquals('60/10', $i->getFocalLength());
      $this->assertEquals('SANYO Electric Co.,Ltd.', $i->getMake());
      $this->assertEquals('f/8.0', $i->getApertureFNumber());
      $this->assertEquals(NULL, $i->getIsoSpeedRatings());
      $this->assertEquals('V06P-74', $i->getSoftware());
      $this->assertEquals(NULL, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('SR6', $i->getModel());
      $this->assertEquals('1998:01:01 00:00:00', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(2, $i->getMeteringMode());
      $this->assertEquals(1, $i->getFlash());
      $this->assertTrue($i->flashUsed());
    }

    /**
     * Test sample image "sanyo-vpcsx550.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleSanyovpcsx550() {
      $i= $this->extractFromFile($this->resourceAsFile('sanyo-vpcsx550.jpg', 'exif_org'));
      $this->assertEquals('10/483', $i->getExposureTime());
      $this->assertEquals('60/10', $i->getFocalLength());
      $this->assertEquals('SANYO Electric Co.,Ltd.', $i->getMake());
      $this->assertEquals('f/2.4', $i->getApertureFNumber());
      $this->assertEquals(400, $i->getIsoSpeedRatings());
      $this->assertEquals('V113p-73', $i->getSoftware());
      $this->assertEquals(NULL, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('SX113', $i->getModel());
      $this->assertEquals('2000:11:18 21:14:19', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(2, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }

    /**
     * Test sample image "sony-cybershot.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleSonyCybershot() {
      $i= $this->extractFromFile($this->resourceAsFile('sony-cybershot.jpg', 'exif_org'));
      $this->assertEquals('1/197', $i->getExposureTime());
      $this->assertEquals('216/10', $i->getFocalLength());
      $this->assertEquals('SONY', $i->getMake());
      $this->assertEquals('f/4.0', $i->getApertureFNumber());
      $this->assertEquals(100, $i->getIsoSpeedRatings());
      $this->assertEquals(NULL, $i->getSoftware());
      $this->assertEquals(2, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(640, $i->getWidth());
      $this->assertEquals(480, $i->getHeight());
      $this->assertEquals('CYBERSHOT', $i->getModel());
      $this->assertEquals('2000:09:30 10:59:45', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(2, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }

    /**
     * Test sample image "sony-d700.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exifSampleSonyD700() {
      $i= $this->extractFromFile($this->resourceAsFile('sony-d700.jpg', 'exif_org'));
      $this->assertEquals(NULL, $i->getExposureTime());
      $this->assertEquals(NULL, $i->getFocalLength());
      $this->assertEquals('SONY', $i->getMake());
      $this->assertEquals('f/2.4', $i->getApertureFNumber());
      $this->assertEquals(200, $i->getIsoSpeedRatings());
      $this->assertEquals(NULL, $i->getSoftware());
      $this->assertEquals(3, $i->getExposureProgram());
      $this->assertEquals(NULL, $i->getWhiteBalance());
      $this->assertEquals(672, $i->getWidth());
      $this->assertEquals(512, $i->getHeight());
      $this->assertEquals('DSC-D700', $i->getModel());
      $this->assertEquals('1998:12:01 14:22:36', $i->getDateTime()->toString('Y:m:d H:i:s'));
      $this->assertEquals(2, $i->getMeteringMode());
      $this->assertEquals(0, $i->getFlash());
      $this->assertFalse($i->flashUsed());
    }
  }
?>
