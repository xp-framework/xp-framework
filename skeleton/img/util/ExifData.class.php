<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'img.ImagingException',
    'img.Image',
    'img.io.StreamReader',
    'io.Stream',
    'lang.ElementNotFoundException'
  );

  /**
   * Reads the EXIF headers from JPEG or TIFF
   *
   * <code>
   *   uses('img.util.ExifData', 'io.File');
   *
   *   // Use empty Exif data as default value when no Exif data is found
   *   echo ExifData::fromFile(new File($filename), ExifData::$EMPTY)->toString();
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.img.ExifDataTest
   * @see      php://exif_read_data
   * @ext      exif
   * @purpose  Utility
   */
  class ExifData extends Object {
    public static
      $EMPTY= NULL;

    public
      $height           = 0,
      $width            = 0,
      $make             = '',
      $model            = '',
      $flash            = 0,
      $orientation      = 0,
      $fileName         = '',
      $fileSize         = 0,
      $mimeType         = '',
      $dateTime         = NULL,
      $apertureFNumber  = '',
      $software         = '',
      $exposureTime     = '',
      $exposureProgram  = 0,
      $whitebalance     = 0,
      $meteringMode     = 0,
      $isoSpeedRatings  = 0,
      $focalLength      = 0;

    static function __static() {
      self::$EMPTY= new self();
    }
    
    /**
     * Lookup helper
     *
     * @param   array<string, var> exif
     * @param   string* key
     * @return  string value or NULL
     */
    protected static function lookup($exif) {
      for ($i= 1, $s= func_num_args(); $i < $s; $i++) {
        $key= func_get_arg($i);
        if (isset($exif[$key])) return $exif[$key];
      }
      return NULL;
    }

    /**
     * Read from a file
     *
     * @param   io.File file
     * @param   var default default void what should be returned in case no data is found
     * @return  img.util.ExifData
     * @throws  lang.FormatException in case malformed meta data is encountered
     * @throws  lang.ElementNotFoundException in case no meta data is available
     * @throws  img.ImagingException in case reading meta data fails
     */
    public static function fromFile(File $file) {
      if (FALSE === getimagesize($file->getURI(), $info)) {
        $e= new ImagingException('Cannot read image information from '.$file->getURI());
        xp::gc(__FILE__);
        throw $e;
      }
      if (!isset($info['APP1'])) {
        if (func_num_args() > 1) return func_get_arg(1);
        throw new ElementNotFoundException(
          'Cannot get EXIF information from '.$file->getURI().' (no APP1 marker)' 
        );
      }
      if (!($info= exif_read_data($file->getURI(), 'COMPUTED,FILE,IFD0,EXIF,COMMENT,MAKERNOTE', TRUE, FALSE))) {
        throw new FormatException('Cannot get EXIF information from '.$file->getURI());
      }
      
      // Change key case for lookups
      foreach ($info as &$val) {
        $val= array_change_key_case($val, CASE_LOWER);
      }
      
      with ($e= new self()); {
      
        // COMPUTED info
        $e->setWidth(self::lookup($info['COMPUTED'], 'width'));
        $e->setHeight(self::lookup($info['COMPUTED'], 'height'));
        $e->setApertureFNumber(self::lookup($info['COMPUTED'], 'aperturefnumber'));
        
        // IFD0 info
        $e->setMake(trim(self::lookup($info['IFD0'], 'make')));
        $e->setModel(trim(self::lookup($info['IFD0'], 'model')));
        $e->setSoftware(self::lookup($info['IFD0'], 'software'));

        if (NULL !== ($o= self::lookup($info['IFD0'], 'orientation'))) {
          $e->setOrientation($o);
        } else {
          $e->setOrientation(($e->width / $e->height) > 1.0
            ? 1   // normal
            : 5   // transpose
          );
        }

        // FILE info
        $e->setFileName(self::lookup($info['FILE'], 'filename'));
        $e->setFileSize(self::lookup($info['FILE'], 'filesize'));
        $e->setMimeType(self::lookup($info['FILE'], 'mimetype'));
        
        // EXIF info
        $e->setExposureTime(self::lookup($info['EXIF'], 'exposuretime'));
        $e->setExposureProgram(self::lookup($info['EXIF'], 'exposureprogram'));
        $e->setMeteringMode(self::lookup($info['EXIF'], 'meteringmode'));
        $e->setIsoSpeedRatings(self::lookup($info['EXIF'], 'isospeedratings'));

        // Sometimes white balance is in MAKERNOTE - e.g. FUJIFILM's Finepix
        if (NULL !== ($w= self::lookup($info['EXIF'], 'whitebalance'))) {
          $e->setWhiteBalance($w);
        } else if (isset($info['MAKERNOTE']) && NULL !== ($w= self::lookup($info['MAKERNOTE'], 'whitebalance'))) {
          $e->setWhiteBalance($w);
        } else {
          $e->setWhiteBalance(NULL);
        }
        
        // Extract focal length. Some models store "80" as "80/1", rip off
        // the divisor "1" in this case.
        if (NULL !== ($l= self::lookup($info['EXIF'], 'focallength'))) {
          sscanf($l, '%d/%d', $n, $frac);
          $e->setFocalLength(1 == $frac ? $n : $n.'/'.$frac);
        } else {
          $e->setFocalLength(NULL);
        }
        
        // Check for Flash and flashUsed keys
        if (NULL !== ($f= self::lookup($info['EXIF'], 'flash'))) {
          $e->setFlash($f);
        } else {
          $e->setFlash(NULL);
        }

        if (NULL !== ($date= self::lookup($info['EXIF'], 'datetimeoriginal', 'datetimedigitized'))) {
          $t= sscanf($date, '%4d:%2d:%2d %2d:%2d:%2d');
          $e->setDateTime(new Date(mktime($t[3], $t[4], $t[5], $t[1], $t[2], $t[0])));
        }
      }
      return $e;
    }

    /**
     * Set Height
     *
     * @param   int height
     */
    public function setHeight($height) {
      $this->height= $height;
    }

    /**
     * Set Height
     *
     * @param   int height
     * @return  img.util.ExifData this
     */
    public function withHeight($height) {
      $this->height= $height;
      return $this;
    }

    /**
     * Get Height
     *
     * @return  int
     */
    public function getHeight() {
      return $this->height;
    }

    /**
     * Set Width
     *
     * @param   int width
     */
    public function setWidth($width) {
      $this->width= $width;
    }

    /**
     * Set Width
     *
     * @param   int width
     * @return  img.util.ExifData this
     */
    public function withWidth($width) {
      $this->width= $width;
      return $this;
    }

    /**
     * Get Width
     *
     * @return  int
     */
    public function getWidth() {
      return $this->width;
    }

    /**
     * Set Make
     *
     * @param   string make
     */
    public function setMake($make) {
      $this->make= $make;
    }

    /**
     * Set Make
     *
     * @param   string make
     * @return  img.util.ExifData this
     */
    public function withMake($make) {
      $this->make= $make;
      return $this;
    }

    /**
     * Get Make
     *
     * @return  string
     */
    public function getMake() {
      return $this->make;
    }

    /**
     * Set Model
     *
     * @param   string model
     */
    public function setModel($model) {
      $this->model= $model;
    }

    /**
     * Set Model
     *
     * @param   string model
     * @return  img.util.ExifData this
     */
    public function withModel($model) {
      $this->model= $model;
      return $this;
    }

    /**
     * Get Model
     *
     * @return  string
     */
    public function getModel() {
      return $this->model;
    }

    /**
     * Set Flash
     *
     * @param   int flash
     */
    public function setFlash($flash) {
      $this->flash= $flash;
    }

    /**
     * Set Flash
     *
     * @param   int flash
     * @return  img.util.ExifData this
     */
    public function withFlash($flash) {
      $this->flash= $flash;
      return $this;
    }

    /**
     * Get Flash. This is a bitmask:
     * <pre>
     *   0 = flash fired
     *   1 = return detected
     *   2 = return able to be detected
     *   3 = unknown
     *   4 = auto used
     *   5 = unknown
     *   6 = red eye reduction used
     * </pre>
     *
     * @return  int
     */
    public function getFlash() {
      return $this->flash;
    }

    /**
     * Set Orientation
     *
     * @param   int orientation
     */
    public function setOrientation($orientation) {
      $this->orientation= $orientation;
    }

    /**
     * Set Orientation
     *
     * @param   int orientation
     * @return  img.util.ExifData this
     */
    public function withOrientation($orientation) {
      $this->orientation= $orientation;
      return $this;
    }

    /**
     * Get Orientation
     *
     * @return  int
     */
    public function getOrientation() {
      return $this->orientation;
    }

    /**
     * Set FileName
     *
     * @param   string fileName
     */
    public function setFileName($fileName) {
      $this->fileName= $fileName;
    }

    /**
     * Set FileName
     *
     * @param   string fileName
     * @return  img.util.ExifData this
     */
    public function withFileName($fileName) {
      $this->fileName= $fileName;
      return $this;
    }

    /**
     * Get FileName
     *
     * @return  string
     */
    public function getFileName() {
      return $this->fileName;
    }

    /**
     * Set FileSize
     *
     * @param   int fileSize
     */
    public function setFileSize($fileSize) {
      $this->fileSize= $fileSize;
    }

    /**
     * Set FileSize
     *
     * @param   int fileSize
     * @return  img.util.ExifData this
     */
    public function withFileSize($fileSize) {
      $this->fileSize= $fileSize;
      return $this;
    }

    /**
     * Get FileSize
     *
     * @return  int
     */
    public function getFileSize() {
      return $this->fileSize;
    }

    /**
     * Set MimeType
     *
     * @param   string mimeType
     */
    public function setMimeType($mimeType) {
      $this->mimeType= $mimeType;
    }

    /**
     * Set MimeType
     *
     * @param   string mimeType
     * @return  img.util.ExifData this
     */
    public function withMimeType($mimeType) {
      $this->mimeType= $mimeType;
      return $this;
    }

    /**
     * Get MimeType
     *
     * @return  string
     */
    public function getMimeType() {
      return $this->mimeType;
    }

    /**
     * Set DateTime
     *
     * @param   util.Date dateTime
     */
    public function setDateTime($dateTime) {
      $this->dateTime= $dateTime;
    }

    /**
     * Set DateTime
     *
     * @param   util.Date dateTime
     * @return  img.util.ExifData this
     */
    public function withDateTime($dateTime) {
      $this->dateTime= $dateTime;
      return $this;
    }

    /**
     * Get DateTime
     *
     * @return  util.Date
     */
    public function getDateTime() {
      return $this->dateTime;
    }
    
    /**
     * Retrieve whether the flash was used.
     *
     * @see     http://www.drewnoakes.com/code/exif/
     * @see     http://www.awaresystems.be/imaging/tiff/tifftags/privateifd/exif/flash.html
     * @return  bool
     */
    public function flashUsed() {
      return 1 == ($this->flash & 1);
    }
    
    /**
     * Returns whether picture is horizontal
     *
     * @see     http://sylvana.net/jpegcrop/exif_orientation.html
     * @return  bool
     */
    public function isHorizontal() {
      return $this->orientation <= 4;
    }

    /**
     * Returns whether picture is vertical
     *
     * @see     http://sylvana.net/jpegcrop/exif_orientation.html
     * @return  bool
     */
    public function isVertical() {
      return $this->orientation > 4;
    }
    
    /**
     * The orientation of the camera relative to the scene, when the 
     * image was captured. The relation of the '0th row' and '0th column' 
     * to visual position is shown as below:
     *
     * <pre>
     *   +---------------------------------+-----------------+
     *   | value | 0th row    | 0th column | human readable  |
     *   +---------------------------------+-----------------+
     *   | 1     | top        | left side  | normal          |
     *   | 2     | top        | right side | flip horizontal |
     *   | 3     | bottom     | right side | rotate 180°     |
     *   | 4     | bottom     | left side  | flip vertical   |
     *   | 5     | left side  | top        | transpose       |
     *   | 6     | right side | top        | rotate 90°      |
     *   | 7     | right side | bottom     | transverse      |
     *   | 8     | left side  | bottom     | rotate 270°     |
     *   +---------------------------------+-----------------+
     *</pre>
     *
     * @return  string
     */
    public function getOrientationString() {
      static $string= array(
        1 => 'normal',
        2 => 'flip_horizonal',
        3 => 'rotate_180',
        4 => 'flip_vertical',
        5 => 'transpose',
        6 => 'rotate_90',
        7 => 'transverse',
        8 => 'rotate_270' 
      );
      return isset($string[$this->orientation]) ? $string[$this->orientation] : '(unknown)';
    }
    
    /**
     * Get degree of rotation (one of 0, 90, 180 or 270)
     *
     * @see     http://sylvana.net/jpegcrop/exif_orientation.html
     * @return  int
     */
    public function getRotationDegree() {
      static $degree= array(
        3 => 180,   // flip
        6 => 90,    // clockwise
        8 => 270    // counterclockwise
      );
      return isset($degree[$this->orientation]) ? $degree[$this->orientation] : 0;
    }
    

    /**
     * Set ApertureFNumber
     *
     * @param   string apertureFNumber
     */
    public function setApertureFNumber($apertureFNumber) {
      $this->apertureFNumber= $apertureFNumber;
    }

    /**
     * Set ApertureFNumber
     *
     * @param   string apertureFNumber
     * @return  img.util.ExifData this
     */
    public function withApertureFNumber($apertureFNumber) {
      $this->apertureFNumber= $apertureFNumber;
      return $this;
    }

    /**
     * Get ApertureFNumber
     *
     * @return  string
     */
    public function getApertureFNumber() {
      return $this->apertureFNumber;
    }

    /**
     * Set Software
     *
     * @param   string software
     */
    public function setSoftware($software) {
      $this->software= $software;
    }

    /**
     * Set Software
     *
     * @param   string software
     * @return  img.util.ExifData this
     */
    public function withSoftware($software) {
      $this->software= $software;
      return $this;
    }

    /**
     * Get Software
     *
     * @return  string
     */
    public function getSoftware() {
      return $this->software;
    }

    /**
     * Set ExposureTime
     *
     * @param   string exposureTime
     */
    public function setExposureTime($exposureTime) {
      $this->exposureTime= $exposureTime;
    }

    /**
     * Set ExposureTime
     *
     * @param   string exposureTime
     * @return  img.util.ExifData this
     */
    public function withExposureTime($exposureTime) {
      $this->exposureTime= $exposureTime;
      return $this;
    }

    /**
     * Get ExposureTime
     *
     * @return  string
     */
    public function getExposureTime() {
      return $this->exposureTime;
    }

    /**
     * Set ExposureProgram
     *
     * @param   int exposureProgram
     */
    public function setExposureProgram($exposureProgram) {
      $this->exposureProgram= $exposureProgram;
    }

    /**
     * Set ExposureProgram
     *
     * @param   int exposureProgram
     * @return  img.util.ExifData this
     */
    public function withExposureProgram($exposureProgram) {
      $this->exposureProgram= $exposureProgram;
      return $this;
    }

    /**
     * Get ExposureProgram
     *
     * @return  int
     */
    public function getExposureProgram() {
      return $this->exposureProgram;
    }
    
    /**
     * Get String describing exposureProgram value.
     *
     * @return  string
     */
    public function getExposureProgramString() {
      static $ep= array(
        0 => 'not defined',
        1 => 'manual',
        2 => 'normal program',
        3 => 'aperture priority',
        4 => 'shutter priority',
        5 => 'creative program',    // (biased toward depth of field)
        6 => 'action program',      // (biased toward fast shutter speed)
        7 => 'portrait mode',       // (for closeup photos with the background out of focus)
        8 => 'landscape mode',      // (for landscape photos with the background in the focus)
      );
      
      return (isset($ep[$this->exposureProgram])
        ? $ep[$this->exposureProgram]
        : 'n/a'
      );
    }    

    /**
     * Set MeteringMode
     *
     * @param   int meteringMode
     */
    public function setMeteringMode($meteringMode) {
      $this->meteringMode= $meteringMode;
    }

    /**
     * Set MeteringMode
     *
     * @param   int meteringMode
     * @return  img.util.ExifData this
     */
    public function withMeteringMode($meteringMode) {
      $this->meteringMode= $meteringMode;
      return $this;
    }

    /**
     * Get MeteringMode
     *
     * @return  int
     */
    public function getMeteringMode() {
      return $this->meteringMode;
    }
    
    /**
     * Get string describing meteringMode value.
     *
     * @return  string
     */
    public function getMeteringModeString() {
      static $mm= array(
        0   => 'unknown',                 
        1   => 'average',                 
        2   => 'center weighted average', 
        3   => 'spot',                    
        4   => 'multispot',               
        5   => 'pattern',                 
        6   => 'partial',                 
        255 => 'other'
      );
      
      return (isset($mm[$this->meteringMode])
        ? $mm[$this->meteringMode]
        : 'n/a'
      );
    }

    /**
     * Set Whitebalance
     *
     * @param   int whitebalance
     */
    public function setWhitebalance($whitebalance) {
      $this->whitebalance= $whitebalance;
    }

    /**
     * Set Whitebalance
     *
     * @param   int whitebalance
     * @return  img.util.ExifData this
     */
    public function withWhitebalance($whitebalance) {
      $this->whitebalance= $whitebalance;
      return $this;
    }

    /**
     * Get Whitebalance.
     * Values are 0 = auto white balance, 1 = manual white balance.
     *
     * @return  int
     */
    public function getWhitebalance() {
      return $this->whitebalance;
    }

    /**
     * Set IsoSpeedRatings
     *
     * @param   int isoSpeedRatings
     */
    public function setIsoSpeedRatings($isoSpeedRatings) {
      $this->isoSpeedRatings= $isoSpeedRatings;
    }

    /**
     * Set IsoSpeedRatings
     *
     * @param   int isoSpeedRatings
     * @return  img.util.ExifData this
     */
    public function withIsoSpeedRatings($isoSpeedRatings) {
      $this->isoSpeedRatings= $isoSpeedRatings;
      return $this;
    }

    /**
     * Get IsoSpeedRatings
     *
     * @return  int
     */
    public function getIsoSpeedRatings() {
      return $this->isoSpeedRatings;
    }

    /**
     * Set FocalLength
     *
     * @param   int FocalLength
     */
    public function setFocalLength($focallength) {
      $this->focalLength= $focallength;
    }

    /**
     * Set FocalLength
     *
     * @param   int FocalLength
     * @return  img.util.ExifData this
     */
    public function withFocalLength($focallength) {
      $this->focalLength= $focallength;
      return $this;
    }

    /**
     * Get FocalLength
     *
     * @return  int
     */
    public function getFocalLength() {
      return $this->focalLength;
    }

    /**
     * Get Thumbnail
     *
     * @return  img.Image  
     */
    public function getThumbnail() {
      $s= new Stream();
      $s->open(STREAM_MODE_WRITE);
      $s->write(exif_thumbnail($this->getFilename()));
      $s->rewind();
      return Image::loadFrom(new StreamReader($s));
    } 

    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(%d x %d %s)@{\n".
        "  [file            ] %s (%d bytes)\n".
        "  [make            ] %s\n".
        "  [model           ] %s\n".
        "  [software        ] %s\n".
        "  [flash           ] %d (%s)\n".
        "  [orientation     ] %s (%s, %s)\n".
        "  [dateTime        ] %s\n".
        "  [apertureFNumber ] %s\n".
        "  [exposureTime    ] %s\n".
        "  [exposureProgram ] %s (%s)\n".
        "  [meteringMode    ] %s (%s)\n".
        "  [whitebalance    ] %s\n".
        "  [isoSpeedRatings ] %s\n".
        "  [focalLength     ] %s\n".
        "}",
        $this->getClassName(),
        $this->width,
        $this->height,
        $this->mimeType,
        $this->fileName,
        $this->fileSize,
        $this->make,
        $this->model,
        $this->software,
        $this->flash, 
        $this->flashUsed() ? 'on' : 'off',
        $this->orientation,
        $this->isHorizontal() ? 'horizontal' : 'vertical',
        $this->getOrientationString(),
        xp::stringOf($this->dateTime),
        $this->apertureFNumber,
        $this->exposureTime,
        $this->exposureProgram,
        $this->getExposureProgramString(),
        $this->meteringMode,
        $this->getMeteringModeString(),
        $this->whitebalance,
        $this->isoSpeedRatings,
        $this->focalLength
      );
    }
  }
?>
