<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.streams.InputStream',
    'img.io.ImageMetaData',
    'img.io.Segment',
    'img.ImagingException'
  );

  /**
   * Reads meta data from JPEG files
   *
   * <code>
   *   $reader= new MetaDataReader();
   *   $meta= $reader->read($file->getInputStream(), $file->getURI());
   *
   *   $exif= $meta->exifData();
   *   $iptc= $meta->iptcData();
   * </code>
   *
   * @see  php://exif_read_data
   * @see  php://iptcparse
   * @see  php://getimagesize
   * @test xp://net.xp_framework.unittest.img.MetaDataReaderTest
   */
  class MetaDataReader extends Object {
    protected static $seg= array(
      "\x01" => 'TEM',   "\x02" => 'RES',

      "\xc0" => 'SOF0',  "\xc1" => 'SOF1',  "\xc2" => 'SOF2',  "\xc3" => 'SOF4',
      "\xc4" => 'DHT',   "\xc5" => 'SOF5',  "\xc6" => 'SOF6',  "\xc7" => 'SOF7',
      "\xc8" => 'JPG',   "\xc9" => 'SOF9',  "\xca" => 'SOF10', "\xcb" => 'SOF11',
      "\xcc" => 'DAC',   "\xcd" => 'SOF13', "\xce" => 'SOF14', "\xcf" => 'SOF15',

      "\xd0" => 'RST0',  "\xd1" => 'RST1',  "\xd2" => 'RST2',  "\xd3" => 'RST3',
      "\xd4" => 'RST4',  "\xd5" => 'RST5',  "\xd6" => 'RST6',  "\xd7" => 'RST7',
      "\xd8" => 'SOI',   "\xd9" => 'EOI',   "\xda" => 'SOS',   "\xdb" => 'DQT',
      "\xdc" => 'DNL',   "\xdd" => 'DRI',   "\xde" => 'DHP',   "\xdf" => 'EXP',

      "\xe0" => 'APP0',  "\xe1" => 'APP1',  "\xe2" => 'APP2',  "\xe3" => 'APP3',
      "\xe4" => 'APP4',  "\xe5" => 'APP5',  "\xe6" => 'APP6',  "\xe7" => 'APP7',
      "\xe8" => 'APP8',  "\xe9" => 'APP9',  "\xea" => 'APP10', "\xeb" => 'APP11',
      "\xec" => 'APP12', "\xed" => 'APP13', "\xee" => 'APP14', "\xef" => 'APP15',

      "\xf0" => 'JPG0',  "\xf1" => 'JPG1',  "\xf2" => 'JPG2',  "\xf3" => 'JPG3',
      "\xf4" => 'JPG4',  "\xf5" => 'JPG5',  "\xf6" => 'JPG6',  "\xf7" => 'JPG7',
      "\xf8" => 'JPG8',  "\xf9" => 'JPG9',  "\xfa" => 'JPG10', "\xfb" => 'JPG11',
      "\xfc" => 'JPG12', "\xfd" => 'JPG13', "\xfe" => 'COM',
    );

    protected $impl= array(
      'SOF0'  => 'img.io.SOFNSegment',    // image width and height
      'APP1'  => 'img.io.APP1Segment',    // Exif, XMP
      'APP13' => 'img.io.APP13Segment',   // IPTC
      'COM'   => 'img.io.CommentSegment'
    );

    /**
     * Returns a segment
     *
     * @param  string $marker
     * @param  string $data
     * @return img.io.Segment
     */
    protected function segmentFor($marker, $data) {
      if (isset(self::$seg[$marker])) {
        $seg= self::$seg[$marker];
        if (isset($this->impl[$seg])) {
          return XPClass::forName($this->impl[$seg])->getMethod('read')->invoke(NULL, array($seg, $data));
        } else {
          return new Segment($seg, $data);
        }
      } else {
        return new Segment(sprintf('0x%02x', ord($marker)), $data);
      }
    }

    /**
     * Reads meta data from the given input stream
     *
     * @param  io.streams.InputStream $in The input stream to read from
     * @param  string $name The input stream's name
     * @return img.io.ImageMetaData
     * @throws img.ImagingException if the input stream cannot be parsed
     */
    public function read(InputStream $in, $name= 'input stream') {
      if ("\xff\xd8\xff" !== $in->read(3)) {
        throw new ImagingException('Could not find start of image marker in JPEG data '.$name);
      }
      $offset= 3;

      // Parse JPEG headers
      $data= new ImageMetaData();
      $data->setSource($name);
      while ("\xd9" !== ($marker= $in->read(1))) {
        $offset++;
        if ("\xda" === $marker) break;      // Stop at SOS (Start Of Scan)

        if ($marker < "\xd0" || $marker > "\xd7") {
          $size= current(unpack('n', $in->read(2)));
          $data->addSegment($this->segmentFor($marker, $in->read($size - 2)));
          $offset+= $size;
        }

        if ("\xff" !== ($c= $in->read(1))) {
          throw new ImagingException(sprintf(
            'JPEG header corrupted, have x%02x, expecting xff at offset %d',
            ord($c),
            $offset
          ));
        }
        $offset++;
      }
      return $data;
    }
  }
?>
