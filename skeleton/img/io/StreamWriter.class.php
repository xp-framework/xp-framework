<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Writes to a stream
   *
   * @ext      gd
   * @see      xp://img.io.ImageWriter
   * @see      xp://img.Image#saveTo
   * @purpose  Abstract base class
   */
  class StreamWriter extends Object {
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
     * Output an image. Abstract method, overwrite in child
     * classes!
     *
     * @model   abstract
     * @access  protected
     * @param   resource handle
     * @return  bool
     */    
    function output($handle) { }
    
    /**
     * Callback function for ob_start
     *
     * @access  private
     * @param   string data
     */
    function writeToStream($data) {
      $this->stream->write($data);
    }

    /**
     * Sets the image resource that is to be written
     *
     * @access  public
     * @param   resource handle
     * @throws  img.ImagingException
     */
    function setResource($handle) {
      try(); {
        $this->stream->open(STREAM_MODE_WRITE);
        
        // Use output buffering with a callback method to capture the 
        // image(gd|jpeg|png|...) functions' output.
        ob_start(array(&$this, 'writeToStream'));
        $r= $this->output($handle);
        ob_end_clean();
        
        $this->stream->close();
        if (!$r) throw(new IOException('Could not write image'));
      } if (catch('IOException', $e)) {
        return throw(new ImagingException($e->getMessage()));
      }
    }
    
  } implements(__FILE__, 'img.io.ImageWriter');
?>
