<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.ImageWriter');

  /**
   * Writes to a stream
   *
   * @ext      gd
   * @see      xp://img.io.ImageWriter
   * @see      xp://img.Image#saveTo
   * @purpose  Abstract base class
   */
  abstract class StreamWriter extends Object implements ImageWriter {
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
     * Output an image. Abstract method, overwrite in child
     * classes!
     *
     * @param   resource handle
     * @return  bool
     */    
    protected abstract function output($handle);
    
    /**
     * Callback function for ob_start
     *
     * @param   string data
     */
    public function writeToStream($data) {
      $this->stream->write($data);
    }

    /**
     * Sets the image resource that is to be written
     *
     * @param   resource handle
     * @throws  img.ImagingException
     */
    public function setResource($handle) {
      try {
        $this->stream->open(STREAM_MODE_WRITE);
        
        // Use output buffering with a callback method to capture the 
        // image(gd|jpeg|png|...) functions' output.
        ob_start(array($this, 'writeToStream'));
        $r= $this->output($handle);
        ob_end_clean();
        
        $this->stream->close();
        if (!$r) throw(new IOException('Could not write image'));
      } catch (IOException $e) {
        throw(new ImagingException($e->getMessage()));
      }
    }
    
  } 
?>
