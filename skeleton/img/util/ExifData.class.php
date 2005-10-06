<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'img.ImagingException', 'img.Image', 'img.io.StreamReader', 'io.Stream');

  /**
   * Reads the EXIF headers from JPEG or TIFF
   *
   * @ext      exif
   * @purpose  Utility
   */
  class ExifData extends Object {
    var
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
      $isoSpeedRatings  = 0;

    /**
     * Read from a file
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &img.util.ExifData
     * @throws  img.ImagingException in case extracting data fails
     */
    function &fromFile(&$file) {
      if (!($info= exif_read_data($file->getURI()))) {
        return throw(new ImagingException(
          'Cannot get EXIF information from '.$file->getURI()
        ));
      }
      
      // Calculate orientation from dimensions if not available
      if (!isset($info['Orientation'])) {
        $info['Orientation']= (($info['COMPUTED']['Width'] / $info['COMPUTED']['Height']) > 1.0
          ? 1   // normal
          : 5   // transpose
        );
      }
      
      with ($e= &new ExifData()); {
        $e->setWidth($info['COMPUTED']['Width']);
        $e->setHeight($info['COMPUTED']['Height']);
        $e->setMake($info['Make']);
        $e->setModel($info['Model']);
        $e->setFlash($info['Flash']);
        $e->setOrientation($info['Orientation']);
        $e->setFileName($info['FileName']);
        $e->setFileSize($info['FileSize']);
        $e->setMimeType($info['MimeType']);
        $e->setApertureFNumber($info['COMPUTED']['apertureFNumber']);
        $e->setSoftware($info['software']);
        $e->setExposureTime($info['ExposureTime']);
        $e->setExposureProgram($info['ExposureProgram']);
        $e->setMeteringMode($info['MeteringMode']);
        $e->setWhiteBalance($info['WhiteBalance']);
        $e->setIsoSpeedRatings($info['ISOSpeedRatings']);
        
        // Find date and time
        foreach (array('DateTime', 'DateTimeOriginal') as $key) {
          if (!isset($info[$key])) continue;

          $t= sscanf($info[$key], '%4d:%2d:%2d %2d:%2d:%2d');
          $e->setDateTime(new Date(mktime($t[3], $t[4], $t[5], $t[1], $t[2], $t[0])));
          break;
        }
      }
      return $e;
    }

    /**
     * Set Height
     *
     * @access  public
     * @param   int height
     */
    function setHeight($height) {
      $this->height= $height;
    }

    /**
     * Get Height
     *
     * @access  public
     * @return  int
     */
    function getHeight() {
      return $this->height;
    }

    /**
     * Set Width
     *
     * @access  public
     * @param   int width
     */
    function setWidth($width) {
      $this->width= $width;
    }

    /**
     * Get Width
     *
     * @access  public
     * @return  int
     */
    function getWidth() {
      return $this->width;
    }

    /**
     * Set Make
     *
     * @access  public
     * @param   string make
     */
    function setMake($make) {
      $this->make= $make;
    }

    /**
     * Get Make
     *
     * @access  public
     * @return  string
     */
    function getMake() {
      return $this->make;
    }

    /**
     * Set Model
     *
     * @access  public
     * @param   string model
     */
    function setModel($model) {
      $this->model= $model;
    }

    /**
     * Get Model
     *
     * @access  public
     * @return  string
     */
    function getModel() {
      return $this->model;
    }

    /**
     * Set Flash
     *
     * @access  public
     * @param   int flash
     */
    function setFlash($flash) {
      $this->flash= $flash;
    }

    /**
     * Get Flash
     *
     * @access  public
     * @return  int
     */
    function getFlash() {
      return $this->flash;
    }

    /**
     * Set Orientation
     *
     * @access  public
     * @param   int orientation
     */
    function setOrientation($orientation) {
      $this->orientation= $orientation;
    }

    /**
     * Get Orientation
     *
     * @access  public
     * @return  int
     */
    function getOrientation() {
      return $this->orientation;
    }

    /**
     * Set FileName
     *
     * @access  public
     * @param   string fileName
     */
    function setFileName($fileName) {
      $this->fileName= $fileName;
    }

    /**
     * Get FileName
     *
     * @access  public
     * @return  string
     */
    function getFileName() {
      return $this->fileName;
    }

    /**
     * Set FileSize
     *
     * @access  public
     * @param   int fileSize
     */
    function setFileSize($fileSize) {
      $this->fileSize= $fileSize;
    }

    /**
     * Get FileSize
     *
     * @access  public
     * @return  int
     */
    function getFileSize() {
      return $this->fileSize;
    }

    /**
     * Set MimeType
     *
     * @access  public
     * @param   string mimeType
     */
    function setMimeType($mimeType) {
      $this->mimeType= $mimeType;
    }

    /**
     * Get MimeType
     *
     * @access  public
     * @return  string
     */
    function getMimeType() {
      return $this->mimeType;
    }

    /**
     * Set DateTime
     *
     * @access  public
     * @param   &util.Date dateTime
     */
    function setDateTime(&$dateTime) {
      $this->dateTime= &$dateTime;
    }

    /**
     * Get DateTime
     *
     * @access  public
     * @return  &util.Date
     */
    function &getDateTime() {
      return $this->dateTime;
    }
    
    /**
     * Retrieve whether the flash was used.
     *
     * @see     http://jalbum.net/forum/thread.jspa?forumID=4&threadID=830&messageID=4438
     * @access  public
     * @return  bool
     */
    function flashUsed() {
      return 1 == ($this->flash % 8);
    }
    
    /**
     * Returns whether picture is horizontal
     *
     * @see     http://sylvana.net/jpegcrop/exif_orientation.html
     * @access  public
     * @return  bool
     */
    function isHorizontal() {
      return $this->orientation <= 4;
    }

    /**
     * Returns whether picture is vertical
     *
     * @see     http://sylvana.net/jpegcrop/exif_orientation.html
     * @access  public
     * @return  bool
     */
    function isVertical() {
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
     * @access  public
     * @return  string
     */
    function getOrientationString() {
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
      return $string[$this->orientation];
    }
    
    /**
     * Get degree of rotation (one of 0, 90, 180 or 270)
     *
     * @see     http://sylvana.net/jpegcrop/exif_orientation.html
     * @access  public
     * @return  int
     */
    function getRotationDegree() {
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
     * @access  public
     * @param   string apertureFNumber
     */
    function setApertureFNumber($apertureFNumber) {
      $this->apertureFNumber= $apertureFNumber;
    }

    /**
     * Get ApertureFNumber
     *
     * @access  public
     * @return  string
     */
    function getApertureFNumber() {
      return $this->apertureFNumber;
    }

    /**
     * Set Software
     *
     * @access  public
     * @param   string software
     */
    function setSoftware($software) {
      $this->software= $software;
    }

    /**
     * Get Software
     *
     * @access  public
     * @return  string
     */
    function getSoftware() {
      return $this->software;
    }

    /**
     * Set ExposureTime
     *
     * @access  public
     * @param   string exposureTime
     */
    function setExposureTime($exposureTime) {
      $this->exposureTime= $exposureTime;
    }

    /**
     * Get ExposureTime
     *
     * @access  public
     * @return  string
     */
    function getExposureTime() {
      return $this->exposureTime;
    }

    /**
     * Set ExposureProgram
     *
     * @access  public
     * @param   int exposureProgram
     */
    function setExposureProgram($exposureProgram) {
      $this->exposureProgram= $exposureProgram;
    }

    /**
     * Get ExposureProgram
     *
     * @access  public
     * @return  int
     */
    function getExposureProgram() {
      return $this->exposureProgram;
    }
    
    /**
     * Get String describing exposureProgram value.
     *
     * @access  public
     * @return  string
     */
    function getExposureProgramString() {
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
     * @access  public
     * @param   int meteringMode
     */
    function setMeteringMode($meteringMode) {
      $this->meteringMode= $meteringMode;
    }

    /**
     * Get MeteringMode
     *
     * @access  public
     * @return  int
     */
    function getMeteringMode() {
      return $this->meteringMode;
    }
    
    /**
     * Get string describing meteringMode value.
     *
     * @access  public
     * @return  string
     */
    function getMeteringModeString() {
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
     * @access  public
     * @param   int whitebalance
     */
    function setWhitebalance($whitebalance) {
      $this->whitebalance= $whitebalance;
    }

    /**
     * Get Whitebalance.
     * Values are 0 = auto white balance, 1 = manual white balance.
     *
     * @access  public
     * @return  int
     */
    function getWhitebalance() {
      return $this->whitebalance;
    }

    /**
     * Set IsoSpeedRatings
     *
     * @access  public
     * @param   int isoSpeedRatings
     */
    function setIsoSpeedRatings($isoSpeedRatings) {
      $this->isoSpeedRatings= $isoSpeedRatings;
    }

    /**
     * Get IsoSpeedRatings
     *
     * @access  public
     * @return  int
     */
    function getIsoSpeedRatings() {
      return $this->isoSpeedRatings;
    }

    /**
     * Get Thumbnail
     *
     * @access  public
     * @return  &img.Image  
     */
    function getThumbnail() {
      $s= new Stream();
      $s->write(exif_thumbnail($this->getFilename()));
      $s->rewind();
      return Image::loadFrom(new StreamReader($s));
    } 

    /**
     * Retrieve a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
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
        $this->dateTime->toString('r'),
        $this->apertureFNumber,
        $this->exposureTime,
        $this->exposureProgram,
        $this->getExposureProgramString(),
        $this->meteringMode,
        $this->getMeteringModeString(),
        $this->whitebalance,
        $this->isoSpeedRatings
      );
    }
  }
?>
