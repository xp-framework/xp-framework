<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.ImageReader', 'io.streams.InputStream', 'io.Stream', 'io.streams.Streams');

  /**
   * Read images from a stream
   *
   * @ext   gd
   * @test  xp://net.xp_framework.unittest.img.ImageReaderTest
   * @see   xp://img.io.ImageReader
   * @see   xp://img.Image#loadFrom
   */
  class StreamReader extends Object implements ImageReader {
    public $stream= NULL;
    protected static $GD_USERSTREAMS_BUG= FALSE;
    protected $reader= NULL;

    static function __static() {
      self::$GD_USERSTREAMS_BUG= (
        version_compare(PHP_VERSION, '5.5.0RC1', '>=') && version_compare(PHP_VERSION, '5.5.1', '<') &&
        0 !== strncmp('WIN', PHP_OS, 3)
      );
    }

    /**
     * Constructor
     *
     * @param   var stream either an io.streams.InputStream or an io.Stream (BC)
     * @throws  lang.IllegalArgumentException when types are not met
     */
    public function __construct($stream) {
      $this->stream= deref($stream);
      if ($this->stream instanceof InputStream) {
        if ($this instanceof img·io·UriReader && !self::$GD_USERSTREAMS_BUG) {
          $this->reader= function($reader, $stream) {
            return $reader->readImageFromUri(Streams::readableUri($stream));
          };
        } else {
          $this->reader= function($reader, $stream) {
            $bytes= '';
            while ($stream->available() > 0) {
              $bytes.= $stream->read();
            }
            $stream->close();
            return $reader->readImageFromString($bytes);
          };
        }
      } else if ($this->stream instanceof Stream) {
        if ($this instanceof img·io·UriReader && !self::$GD_USERSTREAMS_BUG) {
          $this->reader= function($reader, $stream) {
            $stream->open(STREAM_MODE_READ);
            return $reader->readImageFromUri($stream->getURI());
          };
        } else {
          $this->reader= function($reader, $stream) {
            $stream->open(STREAM_MODE_READ);
            $bytes= '';
            do {
              $bytes.= $stream->read();
            } while (!$stream->eof());
            $stream->close();
            return $reader->readImageFromString($bytes);
          };
        }
      } else {
        throw new IllegalArgumentException('Expected either an io.streams.InputStream or an io.Stream, have '.xp::typeOf($this->stream));
      }
    }

    /**
     * Read image via imagecreatefromstring()
     *
     * @param   string bytes
     * @return  resource
     * @throws  img.ImagingException
     */
    public function readImageFromString($bytes) {
      if (FALSE === ($r= imagecreatefromstring($bytes))) {
        $e= new ImagingException('Cannot read image');
        xp::gc(__FILE__);
        throw $e;
      }
      return $r;
    }
    
    /**
     * Retrieve an image resource
     *
     * @return  resource
     * @throws  img.ImagingException
     */
    public function getResource() {
      try {
        return call_user_func($this->reader, $this, $this->stream);
      } catch (IOException $e) {
        throw new ImagingException($e->getMessage());
      }
    }
  } 
?>
