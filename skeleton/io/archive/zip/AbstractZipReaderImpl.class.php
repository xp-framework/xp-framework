<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.InputStream', 
    'io.archive.zip.ZipFileInputStream', 
    'io.archive.zip.Compression',
    'io.archive.zip.ZipDirEntry',
    'io.archive.zip.ZipFileEntry',
    'io.archive.zip.DecipheringInputStream',
    'io.archive.zip.ZipCipher',
    'util.Date'
  );

  /**
   * Abstract base class for zip reader implementations
   *
   * @ext   iconv
   */
  abstract class AbstractZipReaderImpl extends Object {
    public $skip= 0;

    protected $stream= NULL;
    protected $index= array();
    protected $password= NULL;
    protected $position= 0;

    // Signatures
    const EOCD = "\x50\x4b\x05\x06";  // End-Of-Central-Directory
    const FHDR = "\x50\x4b\x03\x04";  // File header
    const DHDR = "\x50\x4b\x01\x02";  // Zip central directory

    /**
     * Creation constructor
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      $this->stream= $stream;
    }

    /**
     * Set password to use when extracting 
     *
     * @param   string password
     */
    public function setPassword($password) {
      $this->password= new ZipCipher();
      $this->password->initialize(iconv('iso-8859-1', 'cp437', $password));
    }

    /**
     * Creates a date from DOS date and time
     *
     * @see     http://www.vsft.com/hal/dostime.htm
     * @param   int date
     * @param   int time
     * @return  util.Date
     */
    protected function dateFromDosDateTime($date, $time) {
      return Date::create(
        (($date >> 9) & 0x7F) + 1980,
        (($date >> 5) & 0x0F),
        $date & 0x1F,
        ($time >> 11) & 0x1F,
        ($time >> 5) & 0x3F,
        ($time << 1) & 0x1E
      );
    }
    
    /**
     * Returns how many bytes can be read from the stream
     *
     * @return  int
     */
    public function streamAvailable() {
      return $this->stream->available();
    }

    /**
     * Reads from a stream
     *
     * @param   int limit
     * @return  string
     */
    public function streamRead($limit) {
      if (0 === $limit) return '';
      $chunk= $this->stream->read($limit);
      $this->position+= strlen($chunk);
      return $chunk;
    }

    /**
     * Sets stream position 
     *
     * @param   int offset absolute offset
     */
    public function streamPosition($offset) {
      if ($offset !== $this->position) {
        $this->streamSeek($offset, SEEK_SET);
        $this->position= $offset;
      }
    }

    /**
     * Seeks a stream
     *
     * @param   int offset absolute offset
     * @param   int whence
     */
    protected abstract function streamSeek($offset, $whence);

    /**
     * Get first entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public abstract function firstEntry();
    
    /**
     * Get next entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public abstract function nextEntry();

    /**
     * Gets current entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function currentEntry() {
      $type= $this->streamRead(4);
      switch ($type) {
        case self::FHDR: {      // Entry
          $header= unpack(
            'vversion/vflags/vcompression/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnamelen/vextralen', 
            $this->streamRead(26)
          );
          if (0 === $header['namelen']) {
          
            // Prevent 0-length read.
            $decoded= '';
          } else {
            $name= (string)$this->streamRead($header['namelen']);
            
            // Decode name from zipfile. If we find general purpose flag bit 11 
            // (EFS), the name is encoded in UTF-8, if not, we try the following: 
            // Decode from utf-8, then try cp437, and if that fails, we will use 
            // it as-is. Do this as certain vendors (Java e.g.) always use utf-8 
            // but do not indicate this via EFS.
            if ($header['flags'] & 2048) {
              $decoded= iconv('utf-8', 'iso-8859-1', $name);
            } else if ('' === ($decoded= @iconv('utf-8', 'iso-8859-1', $name))) {
              if ('' === ($decoded= @iconv('cp437', 'iso-8859-1', $name))) {
                $decoded= $name;
              }
            }
          }
          $extra= $this->streamRead($header['extralen']);
          $date= $this->dateFromDosDateTime($header['date'], $header['time']);
          $this->skip= $header['compressed'];

          // Short-circuit here for directories
          if ('/' === substr($name, -1)) {
            $e= new ZipDirEntry($decoded);
            $e->setLastModified($date);
            $e->setSize($header['uncompressed']);
            return $e;
          }
          
          // Bit 3: If this bit is set, the fields crc-32, compressed 
          // size and uncompressed size are set to zero in the local 
          // header.  The correct values are put in the data descriptor 
          // immediately following the compressed data.
          if ($header['flags'] & 8) {
            if (!isset($this->index[$name])) {
              $position= $this->position;
              $offset= $this->readCentralDirectory();
              $this->streamPosition($position);
            }
            
            if (!isset($this->index[$name])) throw new FormatException('.zip archive broken: cannot find "'.$name.'" in central directory.');
            $header= $this->index[$name];

            // In case we're here, we can be sure to have a 
            // RandomAccessStream - otherwise the central directory
            // could not have been read in the first place. So,
            // we may seek.
            // If we had strict type checking this would not be
            // possible, though.
            // The offset is relative to the file begin - but also skip over the usual parts:
            // * file header signature (4 bytes)
            // * file header (26 bytes)
            // * file extra + file name (variable size)
            $this->streamPosition($header['offset']+ 30 + $header['extralen'] + $header['namelen']);
            
            // Set skip accordingly: 4 bytes data descriptor signature + 12 bytes data descriptor
            $this->skip= $header['compressed']+ 16;
          }

          // Bit 1: The file is encrypted
          if ($header['flags'] & 1) {
            $cipher= new ZipCipher($this->password);
            $preamble= $cipher->decipher((string)$this->stream->read(12));
            
            // Verify            
            if (ord($preamble{11}) !== (($header['crc'] >> 24) & 0xFF)) {
              throw new IllegalArgumentException('The password did not match ('.ord($preamble{11}).' vs. '.(($header['crc'] >> 24) & 0xFF).')');
            }
            
            // Password matches.
            $this->skip-= 12; 
            $header['compressed']-= 12;
            $is= new DecipheringInputStream(new ZipFileInputStream($this, $this->position, $header['compressed']), $cipher);
          } else {
            $is= new ZipFileInputStream($this, $this->position, $header['compressed']);
          }
          
          // Create ZipEntry object and return it
          $e= new ZipFileEntry($decoded);
          $e->setLastModified($date);
          $e->setSize($header['uncompressed']);
          $e->setCompression(Compression::getInstance($header['compression']));
          $e->is= $is;
          return $e;
        }
        case self::DHDR: {      // Zip directory
          return NULL;          // XXX: For the moment, ignore directory and stop here
        }
        case self::EOCD: {      // End of central directory
          return NULL;
        }
      }
      throw new FormatException('Unknown byte sequence '.addcslashes($type, "\0..\17"));
    }
    
    protected function addToIndex($filename, $header) {
      $this->index[$filename]= $header;
    }
    
    /**
     * Read central directory; not supported in this abstract
     * implementation.
     *
     * @return  void
     */
    protected function readCentralDirectory() {
      raise('lang.MethodNotImplementedException', 'Seeking central directory is only supported by RandomAccessZipReaderImpl.');
    }
  }
?>
