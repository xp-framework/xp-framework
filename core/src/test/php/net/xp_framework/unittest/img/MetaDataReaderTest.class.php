<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase', 
    'img.io.MetaDataReader', 
    'img.io.SOFNSegment',
    'img.io.CommentSegment',
    'img.io.XMPSegment'
  );

  /**
   * TestCase for MetaDataReader class
   *
   * @see  xp://img.io.MetaDataReader
   */
  class MetaDataReaderTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Sets up this unittest 
     *
     * @throws  unittest.PrerequisitesNotMetError
     */
    public function setUp() {
      $this->fixture= new MetaDataReader();
    }

    /**
     * Returns a file for a classloader resource
     *
     * @param   string name
     * @param   string sub default NULL subpackage
     * @return  io.File
     */
    protected function resourceAsFile($name, $sub= NULL) {
      $package= $this->getClass()->getPackage();
      $container= $sub ? $package->getPackage($sub) : $package;
      return $container->getResourceAsStream($name);
    }

    /**
     * Extract from file and return the instance
     *
     * @param   string name
     * @param   string sub default NULL subpackage
     * @return  lang.Generic the instance
     */
    protected function extractFromFile($name, $sub= NULL) {
      with ($f= $this->resourceAsFile($name, $sub)); {
        return $this->fixture->read($f->getInputStream(), $name);
      }
    }

    /**
     * Test invoking reader with this class file
     *
     */
    #[@test, @expect(class= 'img.ImagingException', withMessage= '/Could not find start of image/')]
    public function this_class_file() {
      $this->extractFromFile(basename(__FILE__));
    }

    /**
     * Test an empty file
     *
     */
    #[@test, @expect(class= 'img.ImagingException', withMessage= '/Could not find start of image/')]
    public function empty_file() {
      $this->extractFromFile('empty.jpg');
    }

    /**
     * Assertion helper
     * 
     * @param  string $type The expected type
     * @param  int $size The expected size
     * @param  var $value The value
     * @throws unittest.AssertionFailedError
     */
    protected function assertArrayOf($type, $size, $value) {
      $this->assertInstanceOf(new ArrayType($type), $value);
      $this->assertEquals($size, sizeof($value));
    }

    /**
     * Test allSegments() method
     *
     */
    #[@test]
    public function all_segments() {
      $this->assertArrayOf('img.io.Segment', 9, $this->extractFromFile('1x1.jpg')->allSegments());
    }

    /**
     * Test segmentsNamed() method
     *
     */
    #[@test]
    public function segments_named_dqt() {
      $this->assertArrayOf('img.io.Segment', 2, $this->extractFromFile('1x1.jpg')->segmentsNamed('DQT'));
    }

    /**
     * Test segmentsNamed() - the 1x1 JPEG has a "SOF0" segment providing width and height
     *
     */
    #[@test]
    public function segment_named_sof0() {
      $this->assertEquals(
        array(new SOFNSegment('SOF0', array('bits' => 8, 'height' => 1, 'width' => 1, 'channels' => 3))),
        $this->extractFromFile('1x1.jpg')->segmentsNamed('SOF0')
      );
    }

    /**
     * Test segmentsOf() - the 1x1 JPEG has a "SOF0" segment providing width and height
     *
     */
    #[@test]
    public function segment_of_sofn() {
      $this->assertEquals(
        array(new SOFNSegment('SOF0', array('bits' => 8, 'height' => 1, 'width' => 1, 'channels' => 3))),
        $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.SOFNSegment')
      );
    }

    /**
     * Test the 1x1 JPEG has a "COM" segment providing width and height
     *
     */
    #[@test]
    public function com_segment() {
      $this->assertEquals(
        array(new CommentSegment('COM', 'Created with GIMP')),
        $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.CommentSegment')
      );
    }

    /**
     * Test a file with exif data
     *
     */
    #[@test]
    public function xmp_segment() {
      $segments= $this->extractFromFile('xmp.jpg')->segmentsOf('img.io.XMPSegment');
      $this->assertArrayOf('img.io.XMPSegment', 1, $segments);
      $this->assertInstanceOf('xml.Node', this($segments[0]->document()->getElementsByTagName('dc:title'), 0));
    }

    /**
     * Test imageDimensions()
     *
     */
    #[@test]
    public function dimensions_of_1x1_image() {
      $this->assertEquals(array(1, 1), $this->extractFromFile('1x1.jpg')->imageDimensions());
    }

    /**
     * Test imageDimensions()
     *
     */
    #[@test]
    public function dimensions_of_xmp_image() {
      $this->assertEquals(array(640, 480), $this->extractFromFile('canon-ixus.jpg', 'exif_org')->imageDimensions());
    }

    /**
     * Test segmentsOf()
     *
     */
    #[@test]
    public function no_exif_data_segments_in_1x1() {
      $this->assertEquals(array(), $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.ExifSegment'));
    }

    /**
     * Test segmentsOf()
     *
     */
    #[@test]
    public function no_iptc_data_segments_in_1x1() {
      $this->assertEquals(array(), $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.IptcSegment'));
    }

    /**
     * Test exifData()
     *
     */
    #[@test]
    public function no_exif_data_in_1x1() {
      $this->assertNull($this->extractFromFile('1x1.jpg')->exifData());
    }

    /**
     * Test iptcData()
     *
     */
    #[@test]
    public function no_iptc_data_in_1x1() {
      $this->assertNull($this->extractFromFile('1x1.jpg')->iptcData());
    }

    /**
     * Test a file with exif data
     *
     */
    #[@test]
    public function exif_data_segments() {
      $this->assertArrayOf(
        'img.io.ExifSegment', 1, 
        $this->extractFromFile('exif-only.jpg')->segmentsOf('img.io.ExifSegment')
      );
    }

    /**
     * Test a file with exif data
     *
     */
    #[@test]
    public function iptc_data_segments() {
      $this->assertArrayOf(
        'img.io.IptcSegment', 1, 
        $this->extractFromFile('iptc-only.jpg')->segmentsOf('img.io.IptcSegment')
      );
    }

    /**
     * Test a file with exif data
     *
     */
    #[@test]
    public function exif_and_iptc_data_segments() {
      $meta= $this->extractFromFile('exif-and-iptc.jpg');
      $this->assertArrayOf('img.io.ExifSegment', 1, $meta->segmentsOf('img.io.ExifSegment'));
      $this->assertArrayOf('img.io.IptcSegment', 1, $meta->segmentsOf('img.io.IptcSegment'));
    }

    /**
     * Test sample image "canon-ixus.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_CanonIxus() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('canon-ixus.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('1/350')
          ->withExposureTime('1/350')
          ->withFocalLength('346/32')
          ->withMake('Canon')
          ->withApertureFNumber('f/4.0')
          ->withIsoSpeedRatings(NULL)
          ->withSoftware(NULL)
          ->withExposureProgram(NULL)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('Canon DIGITAL IXUS')
          ->withDateTime(new Date('2001:06:09 15:17:32'))
          ->withMeteringMode(2)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('canon-ixus.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "fujifilm-dx10.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_FujifilmDx10() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('fujifilm-dx10.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime(NULL)
          ->withFocalLength('58/10')
          ->withMake('FUJIFILM')
          ->withApertureFNumber('f/4.2')
          ->withIsoSpeedRatings(150)
          ->withSoftware('Digital Camera DX-10 Ver1.00')
          ->withExposureProgram(2)
          ->withWhiteBalance(NULL)
          ->withWidth(1024)
          ->withHeight(768)
          ->withModel('DX-10')
          ->withDateTime(new Date('2001:04:12 20:33:14'))
          ->withMeteringMode(5)
          ->withFlash(1)
          ->withOrientation(1)
        ,
        $this->extractFromFile('fujifilm-dx10.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "fujifilm-finepix40i.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_FujifilmFinepix40i() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('fujifilm-finepix40i.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime(NULL)
          ->withFocalLength('870/100')
          ->withMake('FUJIFILM')
          ->withApertureFNumber('f/2.8')
          ->withIsoSpeedRatings(200)
          ->withSoftware('Digital Camera FinePix40i Ver1.39')
          ->withExposureProgram(2)
          ->withWhiteBalance(0)
          ->withWidth(600)
          ->withHeight(450)
          ->withModel('FinePix40i')
          ->withDateTime(new Date('2000:08:04 18:22:57'))
          ->withMeteringMode(5)
          ->withFlash(1)
          ->withOrientation(1)
        ,
        $this->extractFromFile('fujifilm-finepix40i.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "fujifilm-mx1700.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_FujifilmMx1700() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('fujifilm-mx1700.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime(NULL)
          ->withFocalLength('99/10')
          ->withMake('FUJIFILM')
          ->withApertureFNumber('f/7.0')
          ->withIsoSpeedRatings(125)
          ->withSoftware('Digital Camera MX-1700ZOOM Ver1.00')
          ->withExposureProgram(2)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('MX-1700ZOOM')
          ->withDateTime(new Date('2000:09:02 14:30:10'))
          ->withMeteringMode(5)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('fujifilm-mx1700.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "kodak-dc210.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_KodakDC210() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('kodak-dc210.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('1/30')
          ->withFocalLength('44/10')
          ->withMake('Eastman Kodak Company')
          ->withApertureFNumber('f/4.0')
          ->withIsoSpeedRatings(NULL)
          ->withSoftware(NULL)
          ->withExposureProgram(NULL)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('DC210 Zoom (V05.00)')
          ->withDateTime(new Date('2000:10:26 16:46:51'))
          ->withMeteringMode(2)
          ->withFlash(1)
          ->withOrientation(1)
        ,
        $this->extractFromFile('kodak-dc210.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "kodak-dc240.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_KodakDC240() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('kodak-dc240.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('1/30')
          ->withFocalLength('140/10')
          ->withMake('EASTMAN KODAK COMPANY')
          ->withApertureFNumber('f/4.0')
          ->withIsoSpeedRatings(NULL)
          ->withSoftware(NULL)
          ->withExposureProgram(NULL)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('KODAK DC240 ZOOM DIGITAL CAMERA')
          ->withDateTime(new Date('1999:05:25 21:00:09'))
          ->withMeteringMode(1)
          ->withFlash(1)
          ->withOrientation(1)
        ,
        $this->extractFromFile('kodak-dc240.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "nikon-e950.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_NikonE950() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('nikon-e950.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('10/770')
          ->withFocalLength('128/10')
          ->withMake('NIKON')
          ->withApertureFNumber('f/5.5')
          ->withIsoSpeedRatings(80)
          ->withSoftware('v981-79')
          ->withExposureProgram(2)
          ->withWhiteBalance(0)
          ->withWidth(800)
          ->withHeight(600)
          ->withModel('E950')
          ->withDateTime(new Date('2001:04:06 11:51:40'))
          ->withMeteringMode(5)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('nikon-e950.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "olympus-c960.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_OlympusC960() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('olympus-c960.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('1/345')
          ->withFocalLength('56/10')
          ->withMake('OLYMPUS OPTICAL CO.,LTD')
          ->withApertureFNumber('f/8.0')
          ->withIsoSpeedRatings(125)
          ->withSoftware('OLYMPUS CAMEDIA Master')
          ->withExposureProgram(2)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('C960Z,D460Z')
          ->withDateTime(new Date('2000:11:07 10:41:43'))
          ->withMeteringMode(5)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('olympus-c960.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "ricoh-rdc5300.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_Ricohrdc5300() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('ricoh-rdc5300.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime(NULL)
          ->withFocalLength('133/10')
          ->withMake('RICOH')
          ->withApertureFNumber('f/4.0')
          ->withIsoSpeedRatings(NULL)
          ->withSoftware(NULL)
          ->withExposureProgram(NULL)
          ->withWhiteBalance(NULL)
          ->withWidth(896)
          ->withHeight(600)
          ->withModel('RDC-5300')
          ->withDateTime(new Date('2000:05:31 21:50:40'))
          ->withMeteringMode(NULL)
          ->withFlash(1)
          ->withOrientation(1)
        ,
        $this->extractFromFile('ricoh-rdc5300.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "sanyo-vpcg250.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_SanyoVpcg250() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('sanyo-vpcg250.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('1/171')
          ->withFocalLength('60/10')
          ->withMake('SANYO Electric Co.,Ltd.')
          ->withApertureFNumber('f/8.0')
          ->withIsoSpeedRatings(NULL)
          ->withSoftware('V06P-74')
          ->withExposureProgram(NULL)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('SR6')
          ->withDateTime(new Date('1998:01:01 00:00:00'))
          ->withMeteringMode(2)
          ->withFlash(1)
          ->withOrientation(1)
        ,
        $this->extractFromFile('sanyo-vpcg250.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "sanyo-vpcsx550.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_Sanyovpcsx550() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('sanyo-vpcsx550.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('10/483')
          ->withFocalLength('60/10')
          ->withMake('SANYO Electric Co.,Ltd.')
          ->withApertureFNumber('f/2.4')
          ->withIsoSpeedRatings(400)
          ->withSoftware('V113p-73')
          ->withExposureProgram(NULL)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('SX113')
          ->withDateTime(new Date('2000:11:18 21:14:19'))
          ->withMeteringMode(2)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('sanyo-vpcsx550.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "sony-cybershot.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_SonyCybershot() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('sony-cybershot.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime('1/197')
          ->withFocalLength('216/10')
          ->withMake('SONY')
          ->withApertureFNumber('f/4.0')
          ->withIsoSpeedRatings(100)
          ->withSoftware(NULL)
          ->withExposureProgram(2)
          ->withWhiteBalance(NULL)
          ->withWidth(640)
          ->withHeight(480)
          ->withModel('CYBERSHOT')
          ->withDateTime(new Date('2000:09:30 10:59:45'))
          ->withMeteringMode(2)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('sony-cybershot.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test sample image "sony-d700.jpg" from exif.org
     *
     * @see     http://exif.org/samples.html
     */
    #[@test]
    public function exif_dot_org_sample_SonyD700() {
      $this->assertEquals(
        create(new ExifData())
          ->withFileName('sony-d700.jpg')
          ->withFileSize(-1)
          ->withMimeType('image/jpeg')
          ->withExposureTime(NULL)
          ->withFocalLength(NULL)
          ->withMake('SONY')
          ->withApertureFNumber('f/2.4')
          ->withIsoSpeedRatings(200)
          ->withSoftware(NULL)
          ->withExposureProgram(3)
          ->withWhiteBalance(NULL)
          ->withWidth(672)
          ->withHeight(512)
          ->withModel('DSC-D700')
          ->withDateTime(new Date('1998:12:01 14:22:36'))
          ->withMeteringMode(2)
          ->withFlash(0)
          ->withOrientation(1)
        ,
        $this->extractFromFile('sony-d700.jpg', 'exif_org')->exifData()
      );
    }

    /**
     * Test a file with IPTC data
     *
     */
    #[@test]
    public function detailed_iptc_data() {
      $this->assertEquals(
        create(new IptcData())
          ->withTitle('Unittest Image')
          ->withUrgency(NULL)
          ->withCategory(NULL)
          ->withKeywords(NULL)
          ->withDateCreated(new Date('2011-12-07 00:00:00+0100'))
          ->withAuthor(NULL)
          ->withAuthorPosition(NULL)
          ->withCity(NULL)
          ->withState(NULL)
          ->withCountry(NULL)
          ->withHeadline('Caption')
          ->withCredit('Provider')
          ->withSource('Source')
          ->withCopyrightNotice('Timm Friebe, 2012')
          ->withCaption('Description')
          ->withWriter('Timm')
          ->withSupplementalCategories(NULL)
          ->withSpecialInstructions(NULL)
          ->withOriginalTransmissionReference(NULL)
        ,
        $this->extractFromFile('detailed-iptc-embedded.jpg')->iptcData()
      );
    }

    /**
     * Test a file with IPTC data
     *
     */
    #[@test]
    public function gps_data() {
      $exif= this($this->extractFromFile('gps-embedded.jpg')->segmentsOf('img.io.ExifSegment'), 0);
      $this->assertEquals(2, $exif->rawData('GPS_IFD_Pointer', 'GPSVersion'));
    }
  }
?>
