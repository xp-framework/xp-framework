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
   * @purpose  Base class
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
      $this->stream= &deref($stream);
    }

    /**
     * Read an image.
     *
     * @access  protected
     * @return  resource
     * @throws  img.ImagingException
     */    
    function readFromStream() {
      try(); {
        $this->stream->open(STREAM_MODE_READ);
        $buf= $this->stream->read($this->stream->size());
        $this->stream->close();
      } if (catch('IOException', $e)) {
        return throw(new ImagingException($e->getMessage()));
      }

      return imagecreatefromstring($buf);
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
        $handle= $this->readFromStream();
      } if (catch('ImagingException', $e)) {
        return throw($e);
      }
      if (!is_resource($handle)) {
        return throw(new ImagingException('Cannot read image'));
      }
      return $handle;
    }
    
  } implements(__FILE__, 'img.io.ImageReader');
?>
