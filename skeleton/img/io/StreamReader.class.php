<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Read images from a stream
   *
   * @ext      gd
   * @see      xp://img.io.ImageReader
   * @see      xp://img.Image#loadFrom
   * @purpose  Reader
   */
  class StreamReader extends Object {
    var
      $stream   = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.Stream stream
     */
    function __construct(&$stream) {
      $this->stream= &$stream;
    }

    /**
     * Retrieve an image resource
     *
     * @access  public
     * @return  resource
     * @throws  img.ImagingException
     */
    function getResource() {
      try(); {
        $this->stream->open(STREAM_MODE_READ);
        if (FALSE === ($img= imagecreatefromstring(
          $this->stream->read($this->stream->size()))
        )) {
          throw(new IOException('Cannot read image'));
        }
        $this->stream->close();
      } if (catch('IOException', $e)) {
        return throw(new ImagingException($e->getMessage()));
      }
      return $img;
    }
    
  } implements(__FILE__, 'img.io.ImageReader');
?>
