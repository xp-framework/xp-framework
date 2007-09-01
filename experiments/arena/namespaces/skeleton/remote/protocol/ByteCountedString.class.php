<?php
/* This class is part of the XP framework
 *
 * $Id: ByteCountedString.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote::protocol;

  define('BCS_DEFAULT_CHUNK_SIZE', 0xFFFF);

  /**
   * Byte counted string. The layout is the following:
   *
   * <pre>
   *      1     2     3     4     5   ...
   *   +-----+-----+-----+-----+-----+...+-----+-----+
   *   |   length  | mor |  0  |  1  |...| n-1 |  n  |
   *   +-----+-----+-----+-----+-----+...+-----+-----+
   *   |<--- 3 bytes --->|<-------- n bytes -------->|
   * </pre>
   *
   * The first three bytes are "control bytes":
   * <ul>
   *   <li>The first two bytes contain the chunk's length</li>
   *   <li>The third byte contains whether there are more chunks</li>
   * </ul>
   *
   * The rest of the bytes contains the string.
   * 
   * @purpose  Wire format encoding
   */
  class ByteCountedString extends lang::Object {
    public
      $string= '';
      
    /**
     * Constructor
     *
     * @param   string string default ''
     */
    public function __construct($string= '') {
      $this->string= utf8_encode($string);
    }
    
    /**
     * Return length of encoded string based on specified chunksize
     *
     * @param   int chunksize default BCS_DEFAULT_CHUNK_SIZE
     * @return  int
     */
    public function length($chunksize= ) {
      return strlen($this->string) + 3 * (int)ceil(strlen($this->string) / $chunksize);
    }

    /**
     * Write to a given stream using a specified chunk size
     *
     * @param   io.Stream stream
     * @param   int chunksize default BCS_DEFAULT_CHUNK_SIZE
     */
    public function writeTo($stream, $chunksize= ) {
      $length= strlen($this->string);
      $offset= 0;

      do {
        $chunk= $length > $chunksize ? $chunksize : $length;
        $stream->write(pack('nc', $chunk, $length- $chunk > 0));
        $stream->write(substr($this->string, $offset, $chunk));

        $offset+= $chunk;
        $length-= $chunk;
      } while ($length > 0);
    }
    
    /**
     * Read a specified number of bytes from a given stream
     *
     * @param   io.Stream stream
     * @param   int length
     * @return  string
     */
    protected static function readFully($stream, $length) {
      $return= '';
      while (strlen($return) < $length) {
        if (0 == strlen($buf= $stream->readBinary($length - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    /**
     * Read from a stream
     *
     * @param   io.Stream stream
     * @return  string
     */
    public static function readFrom($stream) {
      $s= '';
      do {
        if (FALSE === ($ctl= unpack('nlength/cnext', self::readFully($stream, 3)))) return;
        $s.= self::readFully($stream, $ctl['length']);
      } while ($ctl['next']);
      
      return utf8_decode($s);
    }
  }
?>
