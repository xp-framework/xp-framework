<?php namespace net\xp_framework\unittest\img;

use unittest\TestCase;
use img\io\MetaDataReader;
use img\io\SOFNSegment;
use img\io\CommentSegment;
use img\io\XMPSegment;

/**
 * TestCase for MetaDataReader class
 *
 * @see  xp://img.io.MetaDataReader
 */
class MetaDataReaderTest extends TestCase {
  protected $fixture= null;

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
   * @param   string $name
   * @param   string $sub default NULL subpackage
   * @return  io.File
   */
  protected function resourceAsFile($name, $sub= null) {
    $package= $this->getClass()->getPackage();
    $container= $sub ? $package->getPackage($sub) : $package;
    return $container->getResourceAsStream($name);
  }

  /**
   * Extract from file and return the instance
   *
   * @param   string $name
   * @param   string $sub default NULL subpackage
   * @return  lang.Generic the instance
   */
  protected function extractFromFile($name, $sub= null) {
    with ($f= $this->resourceAsFile($name, $sub)); {
      return $this->fixture->read($f->getInputStream(), $name);
    }
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
    $this->assertInstanceOf(new \lang\ArrayType($type), $value);
    $this->assertEquals($size, sizeof($value));
  }

  #[@test, @expect(class= 'img.ImagingException', withMessage= '/Could not find start of image/')]
  public function this_class_file() {
    $this->extractFromFile(basename(__FILE__));
  }

  #[@test, @expect(class= 'img.ImagingException', withMessage= '/Could not find start of image/')]
  public function empty_file() {
    $this->extractFromFile('empty.jpg');
  }

  #[@test]
  public function all_segments() {
    $this->assertArrayOf('img.io.Segment', 9, $this->extractFromFile('1x1.jpg')->allSegments());
  }

  #[@test]
  public function segments_named_dqt() {
    $this->assertArrayOf('img.io.Segment', 2, $this->extractFromFile('1x1.jpg')->segmentsNamed('DQT'));
  }

  #[@test]
  public function segment_named_sof0() {
    $this->assertEquals(
      array(new SOFNSegment('SOF0', array('bits' => 8, 'height' => 1, 'width' => 1, 'channels' => 3))),
      $this->extractFromFile('1x1.jpg')->segmentsNamed('SOF0')
    );
  }

  #[@test]
  public function segment_of_sofn() {
    $this->assertEquals(
      array(new SOFNSegment('SOF0', array('bits' => 8, 'height' => 1, 'width' => 1, 'channels' => 3))),
      $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.SOFNSegment')
    );
  }

  #[@test]
  public function com_segment() {
    $this->assertEquals(
      array(new CommentSegment('COM', 'Created with GIMP')),
      $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.CommentSegment')
    );
  }

  #[@test]
  public function xmp_segment() {
    $segments= $this->extractFromFile('xmp.jpg')->segmentsOf('img.io.XMPSegment');
    $this->assertArrayOf('img.io.XMPSegment', 1, $segments);
    $this->assertInstanceOf('xml.Node', this($segments[0]->document()->getElementsByTagName('dc:title'), 0));
  }

  #[@test]
  public function dimensions_of_1x1_image() {
    $this->assertEquals(array(1, 1), $this->extractFromFile('1x1.jpg')->imageDimensions());
  }

  #[@test]
  public function dimensions_of_xmp_image() {
    $this->assertEquals(array(640, 480), $this->extractFromFile('canon-ixus.jpg', 'exif_org')->imageDimensions());
  }

  #[@test]
  public function no_exif_data_segments_in_1x1() {
    $this->assertEquals(array(), $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.ExifSegment'));
  }

  #[@test]
  public function no_iptc_data_segments_in_1x1() {
    $this->assertEquals(array(), $this->extractFromFile('1x1.jpg')->segmentsOf('img.io.IptcSegment'));
  }

  #[@test]
  public function no_exif_data_in_1x1() {
    $this->assertNull($this->extractFromFile('1x1.jpg')->exifData());
  }

  #[@test]
  public function no_iptc_data_in_1x1() {
    $this->assertNull($this->extractFromFile('1x1.jpg')->iptcData());
  }

  #[@test]
  public function exif_data_segments() {
    $this->assertArrayOf(
      'img.io.ExifSegment', 1, 
      $this->extractFromFile('exif-only.jpg')->segmentsOf('img.io.ExifSegment')
    );
  }

  #[@test]
  public function iptc_data_segments() {
    $this->assertArrayOf(
      'img.io.IptcSegment', 1, 
      $this->extractFromFile('iptc-only.jpg')->segmentsOf('img.io.IptcSegment')
    );
  }

  #[@test]
  public function exif_and_iptc_data_segments() {
    $meta= $this->extractFromFile('exif-and-iptc.jpg');
    $this->assertArrayOf('img.io.ExifSegment', 1, $meta->segmentsOf('img.io.ExifSegment'));
    $this->assertArrayOf('img.io.IptcSegment', 1, $meta->segmentsOf('img.io.IptcSegment'));
  }

  #[@test]
  public function exif_dot_org_sample_CanonIxus() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('canon-ixus.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime('1/350')
        ->withExposureTime('1/350')
        ->withFocalLength('346/32')
        ->withMake('Canon')
        ->withApertureFNumber('f/4.0')
        ->withIsoSpeedRatings(null)
        ->withSoftware(null)
        ->withExposureProgram(null)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('Canon DIGITAL IXUS')
        ->withDateTime(new \util\Date('2001:06:09 15:17:32'))
        ->withMeteringMode(2)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('canon-ixus.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_FujifilmDx10() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('fujifilm-dx10.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime(null)
        ->withFocalLength('58/10')
        ->withMake('FUJIFILM')
        ->withApertureFNumber('f/4.2')
        ->withIsoSpeedRatings(150)
        ->withSoftware('Digital Camera DX-10 Ver1.00')
        ->withExposureProgram(2)
        ->withWhiteBalance(null)
        ->withWidth(1024)
        ->withHeight(768)
        ->withModel('DX-10')
        ->withDateTime(new \util\Date('2001:04:12 20:33:14'))
        ->withMeteringMode(5)
        ->withFlash(1)
        ->withOrientation(1)
      ,
      $this->extractFromFile('fujifilm-dx10.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_FujifilmFinepix40i() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('fujifilm-finepix40i.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime(null)
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
        ->withDateTime(new \util\Date('2000:08:04 18:22:57'))
        ->withMeteringMode(5)
        ->withFlash(1)
        ->withOrientation(1)
      ,
      $this->extractFromFile('fujifilm-finepix40i.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_FujifilmMx1700() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('fujifilm-mx1700.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime(null)
        ->withFocalLength('99/10')
        ->withMake('FUJIFILM')
        ->withApertureFNumber('f/7.0')
        ->withIsoSpeedRatings(125)
        ->withSoftware('Digital Camera MX-1700ZOOM Ver1.00')
        ->withExposureProgram(2)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('MX-1700ZOOM')
        ->withDateTime(new \util\Date('2000:09:02 14:30:10'))
        ->withMeteringMode(5)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('fujifilm-mx1700.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_KodakDC210() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('kodak-dc210.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime('1/30')
        ->withFocalLength('44/10')
        ->withMake('Eastman Kodak Company')
        ->withApertureFNumber('f/4.0')
        ->withIsoSpeedRatings(null)
        ->withSoftware(null)
        ->withExposureProgram(null)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('DC210 Zoom (V05.00)')
        ->withDateTime(new \util\Date('2000:10:26 16:46:51'))
        ->withMeteringMode(2)
        ->withFlash(1)
        ->withOrientation(1)
      ,
      $this->extractFromFile('kodak-dc210.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_KodakDC240() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('kodak-dc240.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime('1/30')
        ->withFocalLength('140/10')
        ->withMake('EASTMAN KODAK COMPANY')
        ->withApertureFNumber('f/4.0')
        ->withIsoSpeedRatings(null)
        ->withSoftware(null)
        ->withExposureProgram(null)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('KODAK DC240 ZOOM DIGITAL CAMERA')
        ->withDateTime(new \util\Date('1999:05:25 21:00:09'))
        ->withMeteringMode(1)
        ->withFlash(1)
        ->withOrientation(1)
      ,
      $this->extractFromFile('kodak-dc240.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_NikonE950() {
    $this->assertEquals(
      create(new \img\util\ExifData())
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
        ->withDateTime(new \util\Date('2001:04:06 11:51:40'))
        ->withMeteringMode(5)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('nikon-e950.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_OlympusC960() {
    $this->assertEquals(
      create(new \img\util\ExifData())
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
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('C960Z,D460Z')
        ->withDateTime(new \util\Date('2000:11:07 10:41:43'))
        ->withMeteringMode(5)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('olympus-c960.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_Ricohrdc5300() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('ricoh-rdc5300.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime(null)
        ->withFocalLength('133/10')
        ->withMake('RICOH')
        ->withApertureFNumber('f/4.0')
        ->withIsoSpeedRatings(null)
        ->withSoftware(null)
        ->withExposureProgram(null)
        ->withWhiteBalance(null)
        ->withWidth(896)
        ->withHeight(600)
        ->withModel('RDC-5300')
        ->withDateTime(new \util\Date('2000:05:31 21:50:40'))
        ->withMeteringMode(null)
        ->withFlash(1)
        ->withOrientation(1)
      ,
      $this->extractFromFile('ricoh-rdc5300.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_SanyoVpcg250() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('sanyo-vpcg250.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime('1/171')
        ->withFocalLength('60/10')
        ->withMake('SANYO Electric Co.,Ltd.')
        ->withApertureFNumber('f/8.0')
        ->withIsoSpeedRatings(null)
        ->withSoftware('V06P-74')
        ->withExposureProgram(null)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('SR6')
        ->withDateTime(new \util\Date('1998:01:01 00:00:00'))
        ->withMeteringMode(2)
        ->withFlash(1)
        ->withOrientation(1)
      ,
      $this->extractFromFile('sanyo-vpcg250.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_Sanyovpcsx550() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('sanyo-vpcsx550.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime('10/483')
        ->withFocalLength('60/10')
        ->withMake('SANYO Electric Co.,Ltd.')
        ->withApertureFNumber('f/2.4')
        ->withIsoSpeedRatings(400)
        ->withSoftware('V113p-73')
        ->withExposureProgram(null)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('SX113')
        ->withDateTime(new \util\Date('2000:11:18 21:14:19'))
        ->withMeteringMode(2)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('sanyo-vpcsx550.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_SonyCybershot() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('sony-cybershot.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime('1/197')
        ->withFocalLength('216/10')
        ->withMake('SONY')
        ->withApertureFNumber('f/4.0')
        ->withIsoSpeedRatings(100)
        ->withSoftware(null)
        ->withExposureProgram(2)
        ->withWhiteBalance(null)
        ->withWidth(640)
        ->withHeight(480)
        ->withModel('CYBERSHOT')
        ->withDateTime(new \util\Date('2000:09:30 10:59:45'))
        ->withMeteringMode(2)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('sony-cybershot.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function exif_dot_org_sample_SonyD700() {
    $this->assertEquals(
      create(new \img\util\ExifData())
        ->withFileName('sony-d700.jpg')
        ->withFileSize(-1)
        ->withMimeType('image/jpeg')
        ->withExposureTime(null)
        ->withFocalLength(null)
        ->withMake('SONY')
        ->withApertureFNumber('f/2.4')
        ->withIsoSpeedRatings(200)
        ->withSoftware(null)
        ->withExposureProgram(3)
        ->withWhiteBalance(null)
        ->withWidth(672)
        ->withHeight(512)
        ->withModel('DSC-D700')
        ->withDateTime(new \util\Date('1998:12:01 14:22:36'))
        ->withMeteringMode(2)
        ->withFlash(0)
        ->withOrientation(1)
      ,
      $this->extractFromFile('sony-d700.jpg', 'exif_org')->exifData()
    );
  }

  #[@test]
  public function detailed_iptc_data() {
    $this->assertEquals(
      create(new \img\util\IptcData())
        ->withTitle('Unittest Image')
        ->withUrgency(null)
        ->withCategory(null)
        ->withKeywords(null)
        ->withDateCreated(new \util\Date('2011-12-07 00:00:00+0100'))
        ->withAuthor(null)
        ->withAuthorPosition(null)
        ->withCity(null)
        ->withState(null)
        ->withCountry(null)
        ->withHeadline('Caption')
        ->withCredit('Provider')
        ->withSource('Source')
        ->withCopyrightNotice('Timm Friebe, 2012')
        ->withCaption('Description')
        ->withWriter('Timm')
        ->withSupplementalCategories(null)
        ->withSpecialInstructions(null)
        ->withOriginalTransmissionReference(null)
      ,
      $this->extractFromFile('detailed-iptc-embedded.jpg')->iptcData()
    );
  }

  #[@test]
  public function gps_data() {
    $exif= this($this->extractFromFile('gps-embedded.jpg')->segmentsOf('img.io.ExifSegment'), 0);
    $this->assertEquals(
      array(
        'Version'      => '2/2/0/0',
        'Latitude'     => '48/1/59/1/54669/1000',    // 48° 59' 54,669" North
        'LatitudeRef'  => 'N',
        'Longitude'    => '8/1/23/1/10003/1000',     // 8° 23' 10,003" East
        'LongitudeRef' => 'E'
      ),
      array(
        'Version'      => $exif->rawData('GPS_IFD_Pointer', 'GPSVersion'),
        'Latitude'     => $exif->rawData('GPS_IFD_Pointer', 'GPSLatitude'),
        'LatitudeRef'  => $exif->rawData('GPS_IFD_Pointer', 'GPSLatitudeRef'),
        'Longitude'    => $exif->rawData('GPS_IFD_Pointer', 'GPSLongitude'),
        'LongitudeRef' => $exif->rawData('GPS_IFD_Pointer', 'GPSLongitudeRef')
      )
    );
  }
}
