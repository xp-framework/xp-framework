<?php
/* This class is part of the XP framework
 *
 * $Id: StreamReader.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::io;

  uses('img.io.ImageReader');

  /**
   * Read images from a stream
   *
   * @ext      gd
   * @see      xp://img.io.ImageReader
   * @see      xp://img.Image#loadFrom
   * @purpose  Base class
   */
  class StreamReader extends lang::Object implements ImageReader {
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
      } catch (io::IOException $e) {
        throw(new img::ImagingException($e->getMessage()));
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
      try {
        $handle= $this->readFromStream();
      } catch (img::ImagingException $e) {
        throw($e);
      }
      if (!is_resource($handle)) {
        throw(new img::ImagingException('Cannot read image'));
      }
      return $handle;
    }
    
  } 
?>
