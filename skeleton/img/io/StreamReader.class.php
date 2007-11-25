<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.ImageReader');

  /**
   * Read images from a stream
   *
   * @ext      gd
   * @see      xp://img.io.ImageReader
   * @see      xp://img.Image#loadFrom
   * @purpose  Base class
   */
  class StreamReader extends Object implements ImageReader {
    public
      $stream   = NULL;
    
    /**
     * Constructor
     *
     * @param   io.Stream stream
     */
    public function __construct($stream) {
      $this->stream= deref($stream);
    }

    /**
     * Read an image.
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      try {
        $this->stream->open(STREAM_MODE_READ);
        $buf= $this->stream->read($this->stream->size());
        $this->stream->close();
      } catch (IOException $e) {
        throw(new ImagingException($e->getMessage()));
      }

      return imagecreatefromstring($buf);
    }
    
    /**
     * Retrieve an image resource
     *
     * @return  resource
     * @throws  img.ImagingException
     */
    public function getResource() {
      $handle= $this->readFromStream();
      if (!is_resource($handle)) {
        throw(new ImagingException('Cannot read image'));
      }
      return $handle;
    }
    
  } 
?>
