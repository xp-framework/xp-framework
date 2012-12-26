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

    // From TIFF 6.0 Specification, Image File Directory, subsection "Types"
    const BYTE      = 1;
    const ASCII     = 2;
    const USHORT    = 3;
    const ULONG     = 4;
    const URATIONAL = 5;

    // From TIFF 6.0 Specification, Appendix A: TIFF Tags Sorted by Number
    protected static $tag= array(
      254 => 'NEWSUBFILETYPE', 255 => 'SUBFILETYPE', 256 => 'IMAGEWIDTH', 257 => 'IMAGEHEIGHT',
      258 => 'BITSPERSAMPLE', 259 => 'COMPRESSION',

      262 => 'PHOTOMETRICINTERPRETATION', 263 => 'THRESHHOLDING', 264 => 'CELLWIDTH',
      265 => 'CELLLENGTH', 266 => 'FILLORDER', 269 => 'DOCUMENTNAME',

      270 => 'DESC', 271 => 'MAKE', 272 => 'MODEL', 273 => 'STRIPOFFSETS', 274 => 'ORIENTATION',
      277 => 'SAMPLESPP', 278 => 'ROWSPERSTRIP', 279 => 'STRIPBYTECOUNTS',

      280 => 'MINSAMPLEVAL', 281 => 'MAXSAMPLEVAL', 282 => 'XRESOLUTION', 283 => 'YRESOLUTION',
      284 => 'PLANARCONF', 285 => 'PAGENAME', 286 => 'XPOS', 287 => 'YPOS', 288 => 'FREEOFS',
      289 => 'FREEBYTECOUNTS',

      290 => 'GRAYRESPONSEUNIT', 291 => 'GRAYRESPONSECURVE', 292 => 'T4OPT', 293 => 'T6OPT',
      296 => 'RESOLUTIONUNIT', 297 => 'PAGENUMBER',

      301 => 'TRANSFERFUNC', 305 => 'SOFTWARE', 306 => 'DATETIME',

      315 => 'ARTIST', 316 => 'HOSTCOMPUTER', 317 => 'PREDICTOR', 318 => 'WHITEPOINT',
      319 => 'PRIMARYCHROM',

      320 => 'COLORMAP', 321 => 'HALFTONEHINTS', 322 => 'TILEWIDTH', 323 => 'TILELENGTH',
      324 => 'TILEOFS', 325 => 'TILEBYTECOUNTS',

      332 => 'INKSET', 333 => 'INKNAMES', 334 => 'NUMBEROFINKS', 336 => 'DOTRANGE',
      337 => 'TARGETPRINTER', 338 => 'EXTRASAMPLES', 339 => 'SAMPLEFORMAT',

      340 => 'SMINSAMPLEVAL', 341 => 'SMAXSAMPLEVAL', 342 => 'TRANSFERRANGE',

      512 => 'JPEGPROC', 513 => 'JPEGINTERCHANGEFORMAT', 514 => 'JPEGINTERCHANGEFORMATLEN',
      515 => 'JPEGRESTARTINTV', 517 => 'JPEGLOSSLESSPRED', 518 => 'JEPGPOINTXFORMS',

      520 => 'JPEGDCTABLES', 521 => 'JPEGACTABLES', 529 => 'YCBCRCOEFFICIENTS',

      530 => 'YCBCRSUBSAMPLING', 531 => 'YCBCRPOSITIONING', 532 => 'REFBLACKWHITE',

      0x8298 => 'COPYRIGHT'
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
      $this->name= $name;
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

        // APP 1 "Exif" marker,
        if (!isset($headers['APP1'])) return NULL;

        $offset= 0;
        $header= unpack('a4id/x2nul/a2align', substr($headers['APP1']['data'], $offset, 8));
        $offset+= 8;
        if ('Exif' !== $header['id']) {
          throw new FormatException('No EXIF data in APP1 section');
        }

        // TIFF Header, part 2: Magic number / first IFD offset
        $pack= array(
          'MM' => array(self::USHORT => 'n', self::ULONG => 'N', self::URATIONAL => 'N2', self::ASCII => 'a*'),
          'II' => array(self::USHORT => 'v', self::ULONG => 'V', self::URATIONAL => 'V2', self::ASCII => 'a*'),
        );
        $header= array_merge($header, unpack(
          $pack[$header['align']][self::USHORT].'magic/'.$pack[$header['align']][self::ULONG].'ifd1',
          substr($headers['APP1']['data'], $offset, 6)
        ));
        $offset+= 6;
        if (42 !== $header['magic']) {
          throw new FormatException('Malformed EXIF data - magic number mismatch at offset '.$offset);
        }

        // Read IFDs
        $n= $header['ifd1'];
        $ifd= array();
        do {
          $offset= $n + 6;
          $ifd= array_merge($ifd, $this->readIFD($headers['APP1']['data'], $offset, $pack[$header['align']]));
          $n= current(unpack($pack[$header['align']][self::ULONG], substr($headers['APP1']['data'], $offset, 4)));
        } while ($n > 0);

        // Console::writeLine('IFD', $ifd);

        $data->setFileName($this->name);
        $data->setFileSize(-1);
        $data->setMimeType('image/jpeg');
        $data->setMake($ifd['MAKE']['data']);
        $data->setModel($ifd['MODEL']['data']);
        $data->setSoftware($ifd['SOFTWARE']['data']);
        $data->setDateTime(new Date($ifd['DATETIME']['data']));
      }
      return $data;
    }

    /**
     * Read IFD entries
     *
     * @param  string $data The EXIF data
     * @param  int $offset The offset to start at
     * @param  [:string] $format The unpack() formats
     * @return [:var] IFD
     */
    protected function readIFD($data, &$offset, $format) {
      static $length= array(
         self::BYTE      => 1,
         self::ASCII     => 1,
         self::USHORT    => 2,
         self::ULONG     => 4,
         self::URATIONAL => 8,
         6 => 1,        // Signed Byte
         7 => 1,        // Undefined
         8 => 2,        // Signed Short
         9 => 4,        // Signed Long
        10 => 8,        // Signed Rational
        11 => 4,        // Float
        12 => 8         // Double
      );

      $entries= current(unpack($format[self::USHORT], substr($data, $offset, 2)));
      $offset+= 2;

      $return= array();
      for ($i= 0; $i < $entries; $i++) {
        $entry= unpack(
          $format[self::USHORT].'tag/'.$format[self::USHORT].'type/'.$format[self::ULONG].'size',
          substr($data, $offset, 8)
        );
        $offset+= 8;

        $l= $entry['size'] * $length[$entry['type']];
        if ($l > 4) {
          $entry['offset']= current(unpack($format[self::ULONG], substr($data, $offset, 4)));
          $read= $entry['offset']+ 6;
        } else {
          $entry['offset']= NULL;   // Fit into 4 bytes
          $read= $offset;
        }
        $offset+= 4;
        $entry['data']= current(unpack($format[$entry['type']], substr($data, $entry['offset'] + 6, $l)));

        $t= isset(self::$tag[$entry['tag']]) ? self::$tag[$entry['tag']] : '#'.$entry['tag'];
        $return[$t]= $entry;
      }

      return $return;
    }
  }
?>
