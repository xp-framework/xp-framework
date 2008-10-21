<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.ImageWriter', 'io.streams.OutputStream', 'io.Stream');

  /**
   * Writes to a stream
   *
   * @ext      gd
   * @test     xp://net.xp_framework.unittest.img.ImageWriterTest
   * @see      xp://img.io.ImageWriter
   * @see      xp://img.Image#saveTo
   * @purpose  Abstract base class
   */
  abstract class StreamWriter extends Object implements ImageWriter {
    public $stream= NULL;
    
    /**
     * Constructor
     *
     * @param   mixed stream either an io.streams.OutputStream or an io.Stream (BC)
     * @throws  lang.IllegalArgumentException when types are not met
     */
    public function __construct($stream) {
      $this->stream= deref($stream);
      if ($this->stream instanceof OutputStream) {
        // Already open
      } else if ($this->stream instanceof Stream) {
        $this->stream->open(STREAM_MODE_WRITE);
      } else {
        throw new IllegalArgumentException('Expected either an io.streams.OutputStream or an io.Stream, have '.xp::typeOf($this->stream));
      }
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
     * Sets the image resource that is to be written
     *
     * @param   resource handle
     * @throws  img.ImagingException
     */
    public function setResource($handle) {
      try {
        
        // Use output buffering with a callback method to capture the 
        // image(gd|jpeg|png|...) functions' output.
        ob_start(array($this->stream, 'write'));
        $r= $this->output($handle);
        ob_end_clean();
        
        $this->stream->close();
        if (!$r) throw new ImagingException('Could not write image');
      } catch (IOException $e) {
        throw new ImagingException($e->getMessage());
      }
    }
  } 
?>
