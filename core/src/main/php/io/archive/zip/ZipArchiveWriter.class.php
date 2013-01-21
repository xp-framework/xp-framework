<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.archive.zip.ZipEntry', 
    'io.archive.zip.Compression', 
    'io.archive.zip.ZipFileOutputStream',
    'io.archive.zip.CipheringZipFileOutputStream',
    'io.archive.zip.ZipCipher'
  );


  /**
   * Writes to a ZIP archive
   *
   * @see      xp://io.archive.zip.ZipArchive#create
   * @purpose  Write to a zip archive
   */
  class ZipArchiveWriter extends Object {
    protected
      $stream   = NULL,
      $dir      = array(), 
      $pointer  = 0,
      $out      = NULL,
      $unicode  = FALSE,
      $password = NULL;

    const EOCD = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    const FHDR = "\x50\x4b\x03\x04";
    const DHDR = "\x50\x4b\x01\x02";

    /**
     * Creation constructor
     *
     * @param   io.streams.OutputStream stream
     * @param   bool unicode whether to use unicode for entry names
     */
    public function __construct(OutputStream $stream, $unicode= FALSE) {
      $this->stream= $stream;
      $this->unicode= $unicode;
    }
    
    /**
     * Set whether to use unicode for entry names. Note this is not supported 
     * by for example Windows explorer or the "unzip" command line utility,
     * although the Language Encoding (EFS) bit is set - 7-zip, on the other
     * side, as also this implementation, will handle the name correctly. 
     * Java's jar utility will even expect utf-8, and choke on any other names!
     *
     * @param   bool unicode TRUE to use unicode, false otherwise
     * @return  io.archive.zip.ZipArchiveWriter
     */
    public function usingUnicodeNames($unicode= TRUE) {
      $this->unicode= $unicode;
      return $this;
    }

    /**
     * Set password to use when adding entries 
     *
     * @param   string password
     * @return  io.archive.zip.ZipArchiveWriter this
     */
    public function usingPassword($password) {
      $this->password= new ZipCipher();
      $this->password->initialize(iconv(xp::ENCODING, 'cp437', $password));
      return $this;
    }

    /**
     * Adds a directory entry
     *
     * @param   io.archive.zip.ZipDirEntry entry
     * @return  io.archive.zip.ZipDirEntry the added directory
     * @throws  lang.IllegalArgumentException in case the filename is longer than 65535 bytes
     */
    public function addDir(ZipDirEntry $entry) {
      if (strlen($entry->getName()) > 0xFFFF) {
        throw new IllegalArgumentException('Filename too long ('.$nameLength.')');
      }

      $this->out && $this->out->close();
      $this->out= NULL;
      
      $mod= $entry->getLastModified();
      $name= iconv(xp::ENCODING, $this->unicode ? 'utf-8' : 'cp437', str_replace('\\', '/', $entry->getName()));
      $nameLength= strlen($name);
      $extraLength= 0;
      $extra= '';
      
      $info= pack(
        'vvvvvVVVvv',
        10,                       // version
        $this->unicode ? 2048 : 0,// flags
        0,                        // compression method
        $this->dosTime($mod),     // last modified dostime
        $this->dosDate($mod),     // last modified dosdate
        0,                        // CRC32 checksum
        0,                        // compressed size
        0,                        // uncompressed size
        $nameLength,              // filename length
        $extraLength              // extra field length
      );

      $this->stream->write(self::FHDR.$info.$name);
      $extraLength && $this->stream->write($extra);
      
      $this->dir[$name]= array('info' => $info, 'pointer' => $this->pointer, 'type' => 0x10);
      $this->pointer+= (
        strlen(self::FHDR) + 
        strlen($info) + 
        $nameLength
      );
      
      return $entry;
    }

    /**
     * Adds a file entry
     *
     * @param   io.archive.zip.ZipFileEntry entry
     * @return  io.archive.zip.ZipFileEntry entry
     * @throws  lang.IllegalArgumentException in case the filename is longer than 65535 bytes
     */
    public function addFile(ZipFileEntry $entry) {
      if (strlen($entry->getName()) > 0xFFFF) {
        throw new IllegalArgumentException('Filename too long ('.$nameLength.')');
      }

      $this->out && $this->out->close();

      if ($this->password) {
        $cipher= new ZipCipher($this->password);
        $this->out= new CipheringZipFileOutputStream($this, $entry, $cipher);
      } else {
        $this->out= new ZipFileOutputStream($this, $entry);
      }
      
      $entry->os= $this->out;
      return $entry;
    }
    
    /**
     * Returns a time in the format used by MS-DOS.
     *
     * @see     http://www.vsft.com/hal/dostime.htm
     * @param   util.Date date
     * @return  int
     */
    protected function dosTime(Date $date) {
      return 
        (((($date->getHours() & 0x1F) << 6) | ($date->getMinutes() & 0x3F)) << 5) | 
        ((int)($date->getSeconds() / 2) & 0x1F)
      ;
    }

    /**
     * Returns a date in the format used by MS-DOS.
     *
     * @see     http://www.vsft.com/hal/dostime.htm
     * @param   util.Date date
     * @return  int
     */
    protected function dosDate(Date $date) {
      return
        ((((($date->getYear() - 1980) & 0x7F) << 4) | ($date->getMonth() & 0x0F)) << 5) |
        ($date->getDay() & 0x1F)
      ;
    }

    /**
     * Writes to a stream
     *
     * @param   string arg
     */
    public function streamWrite($arg) {
      $this->stream->write($arg);
    }
    
    /**
     * Write a file entry
     *
     * @param   io.archive.zip.ZipFile file
     * @param   int size
     * @param   int compressed
     * @param   int crc32
     * @param   int flags
     */
    public function writeFile($file, $size, $compressed, $crc32, $flags) {
      $mod= $file->getLastModified();
      $name= iconv(xp::ENCODING, $this->unicode ? 'utf-8' : 'cp437', str_replace('\\', '/', $file->getName()));
      $nameLength= strlen($name);
      $method= $file->getCompression()->ordinal();
      $extraLength= 0;
      $extra= '';

      $info= pack(
        'vvvvvVVVvv',
        10,                       // version
        $this->unicode ? 2048 : $flags,
        $method,                  // compression method, 0 = none, 8 = gz, 12 = bz
        $this->dosTime($mod),     // last modified dostime
        $this->dosDate($mod),     // last modified dosdate
        $crc32,                   // CRC32 checksum
        $compressed,              // compressed size
        $size,                    // uncompressed size
        $nameLength,              // filename length
        $extraLength              // extra field length
      );

      $this->stream->write(self::FHDR.$info);
      $this->stream->write($name);
      $extraLength && $this->stream->write($extra);

      $this->dir[$name]= array('info' => $info, 'pointer' => $this->pointer, 'type' => 0x20);
      $this->pointer+= (
        strlen(self::FHDR) + 
        strlen($info) + 
        $nameLength + 
        $compressed
      );
    }

    /**
     * Closes this zip archive
     *
     */
    public function close() {
      $this->out && $this->out->close();

      $comment= '';
      
      // Build central directory
      $l= 0;
      foreach ($this->dir as $name => $entry) {
        $s= (
          self::DHDR.
          "\x14\x0b".           // version made by
          $entry['info'].       // { see writeFile() }
          "\x00\x00".           // file comment length
          "\x00\x00".           // disk number start
          "\x01\x00".           // internal file attributes
          pack('V', $entry['type']).
          pack('V', $entry['pointer']).
          $name
        );
        $l+= strlen($s);
        $this->stream->write($s);
      }
      
      // End of central directory
      $this->stream->write(self::EOCD);
      $this->stream->write(pack(
        'vvVVv', 
        sizeof($this->dir),     // total #entries in central dir on this disk
        sizeof($this->dir),     // total #entries in central dir
        $l,                     // size of central dir
        $this->pointer,         // offset of start of central directory with respect to the starting disk number
        strlen($comment)
      ));
      $this->stream->write($comment);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'('.$this->stream->toString().")@{\n";
      foreach ($this->dir as $name => $entry) {
        $s.= '  dir{'.dechex($entry['type']).': "'.$name.'" @ '.$entry['pointer']."}\n";
      }
      return $s.'}';
    }
  }
?>
