<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream', 'img.util.ExifData');

  /**
   * Reads the EXIF headers from JPEG or TIFF
   *
   * @see      php://exif_read_data
   */
  class ExifDataReader extends Object {
    protected static $seg= array(
      "\xC0" => 'SOF0',  "\xC1" => 'SOF1',  "\xC2" => 'SOF2',  "\xC3" => 'SOF4',
      "\xC5" => 'SOF5',  "\xC6" => 'SOF6',  "\xC7" => 'SOF7',  "\xC8" => 'JPG',
      "\xC9" => 'SOF9',  "\xCA" => 'SOF10', "\xCB" => 'SOF11', "\xCD" => 'SOF13',
      "\xCE" => 'SOF14', "\xCF" => 'SOF15',
      "\xC4" => 'DHT',   "\xCC" => 'DAC',

      "\xD0" => 'RST0',  "\xD1" => 'RST1',  "\xD2" => 'RST2',  "\xD3" => 'RST3',
      "\xD4" => 'RST4',  "\xD5" => 'RST5',  "\xD6" => 'RST6',  "\xD7" => 'RST7',

      "\xD8" => 'SOI',   "\xD9" => 'EOI',   "\xDA" => 'SOS',   "\xDB" => 'DQT',
      "\xDC" => 'DNL',   "\xDD" => 'DRI',   "\xDE" => 'DHP',   "\xDF" => 'EXP',

      "\xE0" => 'APP0',  "\xE1" => 'APP1',  "\xE2" => 'APP2',  "\xE3" => 'APP3',
      "\xE4" => 'APP4',  "\xE5" => 'APP5',  "\xE6" => 'APP6',  "\xE7" => 'APP7',
      "\xE8" => 'APP8',  "\xE9" => 'APP9',  "\xEA" => 'APP10', "\xEB" => 'APP11',
      "\xEC" => 'APP12', "\xED" => 'APP13', "\xEE" => 'APP14', "\xEF" => 'APP15',


      "\xF0" => 'JPG0',  "\xF1" => 'JPG1',  "\xF2" => 'JPG2',  "\xF3" => 'JPG3',
      "\xF4" => 'JPG4',  "\xF5" => 'JPG5',  "\xF6" => 'JPG6',  "\xF7" => 'JPG7',
      "\xF8" => 'JPG8',  "\xF9" => 'JPG9',  "\xFA" => 'JPG10', "\xFB" => 'JPG11',
      "\xFC" => 'JPG12', "\xFD" => 'JPG13',

      "\xFE" => 'COM',   "\x01" => 'TEM',   "\x02" => 'RES'
    );

    /**
     * Creates a new EXIF data reader instance
     * *
     * @param io.streams.InputStream $in The input stream to read from
     * @param string $name The input stream's name
     */
    public function __construct(InputStream $in, $name= 'input stream') {
      if ("\xff\xd8\xff" !== $in->read(3)) {
        throw new FormatException('Could not find start of image marker in JPEG data '.$name);
      }
      $this->offset= 3;
      $this->stream= $in;
    }

    /**
     * Reads the data
     *
     * @return img.util.ExifData
     */
    public function read() {
      with ($data= new ExifData()); {

        // Parse JPEG headers
        $headers= array();
        while ("\xd9" !== ($marker= $this->stream->read(1))) {
          $this->offset++;
          if ($marker < "\xd0" || $marker > "\xd7") {
            $size= current(unpack('n', $this->stream->read(2)));
            $headers[self::$seg[$marker]]= array(
              'type'   => $marker,
              'offset' => $this->offset,
              'size'   => $size,
              'data'   => $this->stream->read($size - 2)
            );
            $this->offset+= $size;
          }

          // Stop at SOS (Start Of Scan)
          if ("\xda" === $marker) break;
          if ("\xff" !== ($c= $this->stream->read(1))) {
            throw new FormatException(sprintf(
              'JPEG header corrupted, have x%02x, expecting xff at offset %d',
              ord($c),
              $this->offset
            ));
          }
          $this->offset++;
        }

        // Interpret
        if (!isset($headers['APP1'])) return NULL;
      }
      return $data;
    }
  }
?>
