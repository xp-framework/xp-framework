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
    'util.Date'
  );

  /**
   * Abstract base class for zip reader implementations
   *
   * @ext   iconv
   */
  abstract class AbstractZipReaderImpl extends Object {
    protected $stream= NULL;
    public $skip= 0;

    const EOCD = "\x50\x4b\x05\x06";
    const FHDR = "\x50\x4b\x03\x04";
    const DHDR = "\x50\x4b\x01\x02";

    /**
     * Creation constructor
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      $this->stream= $stream;
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
      return $this->stream->read($limit);
    }

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
      $type= $this->stream->read(4);
      switch ($type) {
        case self::FHDR: {      // Entry
          $header= unpack(
            'vversion/vflags/vcompression/vtime/vdate/Vcrc/Vcompressed/Vuncompressed/vnamelen/vextralen', 
            $this->stream->read(26)
          );
          
          if (0 === $header['namelen']) {
          
            // Prevent 0-length read.
            $decoded= '';
          } else {
            $name= $this->stream->read($header['namelen']);

            // Decode name from zipfile. If it cannot be decoded from cp437 
            // we will use it as-is.
            if ('' === ($decoded= @iconv('cp437', 'iso-8859-1', $name))) {
              $decoded= $name;
            }
          }
          $extra= $this->stream->read($header['extralen']);
          $date= $this->dateFromDosDateTime($header['date'], $header['time']);
          
          // Bit 3: If this bit is set, the fields crc-32, compressed 
          // size and uncompressed size are set to zero in the local 
          // header.  The correct values are put in the data descriptor 
          // immediately following the compressed data.
          if ($header['flags'] & 8) {
            raise('lang.MethodNotImplementedException', 'Data descriptors not yet implemented', 8);
          }
          
          $this->skip= $header['compressed'];
          
          // Create ZipEntry object and return it
          if ('/' === substr($name, -1)) {
            return new ZipDirEntry($decoded, $date, $header['uncompressed']);
          } else {
            $e= new ZipFileEntry($decoded);
            $e->setLastModified($date);
            $e->setSize($header['uncompressed']);
            $e->setCompression(Compression::getInstance($header['compression']));
            $e->is= new ZipFileInputStream($this, $header['compressed']);
            return $e;
          }
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
  }
?>
