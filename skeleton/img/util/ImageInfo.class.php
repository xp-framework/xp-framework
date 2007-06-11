<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('img.ImagingException');

  /**
   * Image information
   *
   * @see      php://getimagesize
   * @purpose  Utility
   */
  class ImageInfo extends Object {
    public
      $width      = 0,
      $height     = 0,
      $type       = 0,
      $mime       = '',
      $bits       = NULL,
      $channels   = NULL,
      $segments   = array();

    /**
     * Retrieve an ImageInfo object from a file
     *
     * @param   io.File file
     * @return  img.util.ImageInfo
     * @throws  img.ImagingException in case extracting information from image file fails
     */
    public static function fromFile($file) {
      if (FALSE === ($data= getimagesize($file->getURI(), $segments))) {
        throw(new ImagingException(
          'Cannot load image information from '.$file->getURI()
        ));
      }
      
      with ($i= new ImageInfo()); {
        $i->width= $data[0];
        $i->height= $data[1];
        $i->type= $data[2];
        $i->mime= image_type_to_mime_type($data[2]);
        isset($data['bits']) && $i->bits= $data['bits'];
        isset($data['channels']) && $i->channels= $data['channels'];
        $i->segments= $segments;
      }
      return $i;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(%d x %d %s)@{\n".
        "  [type       ] %d\n".
        "  [channels   ] %s\n".
        "  [bits       ] %s\n".
        "  [segments   ] %s\n".
        "}",
        $this->getClassName(),
        $this->width,
        $this->height,
        $this->mime,
        $this->type,
        NULL === $this->channels ? '(unknown)' : $this->channels,
        NULL === $this->bits ? '(unknown)' : $this->bits,
        implode(', ', array_keys($this->segments))
      );
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
     * Get Width
     *
     * @return  int
     */
    public function getWidth() {
      return $this->width;
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
     * Get Height
     *
     * @return  int
     */
    public function getHeight() {
      return $this->height;
    }

    /**
     * Set Type
     *
     * @param   int type
     */
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @return  int
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Set Bits
     *
     * @param   int bits
     */
    public function setBits($bits) {
      $this->bits= $bits;
    }

    /**
     * Get Bits
     *
     * @return  int
     */
    public function getBits() {
      return $this->bits;
    }

    /**
     * Set Channels
     *
     * @param   int channels
     */
    public function setChannels($channels) {
      $this->channels= $channels;
    }

    /**
     * Get Channels
     *
     * @return  int
     */
    public function getChannels() {
      return $this->channels;
    }

    /**
     * Set Mime
     *
     * @param   string mime
     */
    public function setMime($mime) {
      $this->mime= $mime;
    }

    /**
     * Get Mime
     *
     * @return  string
     */
    public function getMime() {
      return $this->mime;
    }
    
    /**
     * Retrieve whether a specified segment is available
     *
     * @see     http://www.ozhiker.com/electronics/pjmt/jpeg_info/app_segments.html
     * @param   string id the segment's name
     * @return  bool
     */    
    public function hasSegment($id) {
      return isset($this->segments[$id]);
    }

    /**
     * Retrieve all segment names
     *
     * @return  string[]
     */    
    public function getSegmentNames() {
      return array_keys($this->segments);
    }

    /**
     * Retrieve segment data for a specified segment.
     *
     * @param   string id the segment's name
     * @return  bool
     * @throws  img.ImagingException when the specified segment is not available
     */    
    public function getSegment($id) {
      if (!isset($this->segments[$id])) throw(new ImagingException(
        'Segment "'.$id.'" not available'
      ));

      return $this->segments[$id];
    }
  }
?>
