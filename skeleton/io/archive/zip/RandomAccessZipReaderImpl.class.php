<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.AbstractZipReaderImpl', 'io.streams.Seekable');

  /**
   * Zip archive reader that works on Seekable input streams.
   *
   */
  class RandomAccessZipReaderImpl extends AbstractZipReaderImpl {

    /**
     * Creation constructor
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      parent::__construct(cast($stream, 'io.streams.Seekable'));
    }
    
    /**
     * Get first entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function firstEntry() {
      $this->stream->seek(0, SEEK_SET);
      return $this->currentEntry();
    }
    
    /**
     * Get next entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function nextEntry() {
      $this->skip && $this->stream->seek($this->skip, SEEK_CUR);
      return $this->currentEntry();
    }

    /**
     * Read central directory
     *
     */
    protected function readCentralDirectory() {
      $entries= $this->seekCentralDirectory();

      // File pointer is positioned at start of the central directory
      while (self::EOCD !== ($sig= (string)$this->stream->read(4))) {
        if (self::DHDR != $sig) 
          throw new FormatException('No central directory header signature found, have: '.addcslashes($sig, "\0..\17"));

        $header= unpack(
          'vmade/vversion/vflags/vcompression/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnamelen/vextralen/vcommentlen/vdiskno/vattr/Vextattr/Voffset', 
          $this->stream->read(42)
        );
        
        $filename= (string)$this->stream->read($header['namelen']);
        $extra= (string)$this->stream->read($header['extralen']);
        $comment= (string)$this->stream->read($header['commentlen']);

        $this->addToIndex($filename, $header);
      }
    }
    
    protected function seekCentralDirectory() {

      // Seek to start of file, so we can determine filesize
      $this->stream->seek(0, SEEK_SET);
      $fileSize= $this->stream->available();
      
      // Seek to "first" position where EOCD can occur (Note: a file
      // comment may be embedded in the EOCD header - with variable size;
      // this code currently does not support this and just assumes the
      // comment has 0 byte length.)
      $offset= $fileSize- 22;
      if ($offset < 0) throw new FormatException('File too short for a .zip');
      $this->stream->seek($offset, SEEK_SET);
      
      // By reading one byte at a time, try to find the magic marker sequence
      // which indicates the start of the EOCD section
      $marker= (string)$this->stream->read(3);
      while ($this->stream->available()) {
        $marker.= (string)$this->stream->read(1);
        if ($marker == parent::EOCD) break;
        $marker= substr($marker, -3);
      }
      
      if (0 == $this->stream->available()) 
        throw new FormatException('Could not find central directory; currently not supporting archives w/ file comments.');
      
      // Read offset of central directory from end-of-central-directory "header"
      $offset= unpack('vdisk/vstart/vtotal/ventries/Vsize/Voffset', $this->stream->read(16));
      $this->stream->seek($offset['offset'], SEEK_SET);
    }
  }
?>
