<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.ImageReader', 'io.streams.InputStream', 'io.Stream', 'io.streams.Streams');

  /**
   * Read images from a stream
   *
   * @ext      gd
   * @test     xp://net.xp_framework.unittest.img.ImageReaderTest
   * @see      xp://img.io.ImageReader
   * @see      xp://img.Image#loadFrom
   * @purpose  Base class
   */
  class StreamReader extends Object implements ImageReader {
    public $stream= NULL;
    
    /**
     * Constructor
     *
     * @param   var stream either an io.streams.OutputStream or an io.Stream (BC)
     * @throws  lang.IllegalArgumentException when types are not met
     */
    public function __construct($stream) {
      $this->stream= deref($stream);
      if ($this->stream instanceof InputStream) {
        // Already open
      } else if ($this->stream instanceof Stream) {
        $this->stream->open(STREAM_MODE_READ);
      } else {
        throw new IllegalArgumentException('Expected either an io.streams.InputStream or an io.Stream, have '.xp::typeOf($this->stream));
      }
    }

    /**
     * Read an image from an io.Stream object
     *
     * @deprecated
     * @return  resource
     */    
    public function readFromStream() {
      $buf= $this->stream->read($this->stream->size());
      $this->stream->close();

      return imagecreatefromstring($buf);
    }

    /**
     * Read an image
     *
     * @return  resource
     */    
    public function readImage() {
      $bytes= '';
      while ($this->stream->available() > 0) {
        $bytes.= $this->stream->read();
      }
      $this->stream->close();

      return imagecreatefromstring($bytes);
    }
    
    /**
     * Retrieve an image resource
     *
     * @return  resource
     * @throws  img.ImagingException
     */
    public function getResource() {
      try {
        if ($this->stream instanceof InputStream) {
          $handle= $this->readImage();
        } else {
          $handle= $this->readFromStream();
        }
        if (!$handle) throw new ImagingException('Cannot read image');
      } catch (IOException $e) {
        throw new ImagingException($e->getMessage());
      }

      return $handle;
    }
  } 
?>
