<?php
/* This class is part of the XP framework
 *
 * $Id: BIFFWriter.class.php 8975 2006-12-27 18:06:40Z friebe $
 */

  namespace com::microsoft::format;
 
  define('BIFF_LITTLE_ENDIAN',  0x0000);
  define('BIFF_BIG_ENDIAN',     0x0001);
  
  define('BOF_TYPE_WORKBOOK',   0x0005);
  define('BOF_TYPE_WORKSHEET',  0x0010);

  /**
   * BIFF writer
   *
   * <quote>
   * BIFF (BInary File Format) is the file format in which Excel documents are
   * saved on disk.  A BIFF file is a complete description of an Excel document.
   * BIFF files consist of sequences of variable-length records. There are many
   * different types of BIFF records.  For example, one record type describes a
   * formula entered into a cell; one describes the size and location of a
   * window into a document; another describes a picture format.
   * </quote>
   *
   * @purpose  Write Excel (Biff) files
   */
  class BIFFWriter extends lang::Object {
    public 
      $version      = 0x0500;
      
    public
      $_data        = '',
      $_datasize    = 0,
      $_limit       = 2080,
      $_byteorder   = -1;
    
    /**
     * Constructor
     *
     * @throws  lang.FormatException if byte order (big / little endian) cannot be determined
     */  
    public function __construct() {
      $s= pack('d', 1.2345);
      $n= pack("C8", 0x8D, 0x97, 0x6E, 0x12, 0x83, 0xC0, 0xF3, 0x3F);
      if ($n == $s) {
        $this->_byteorder= BIFF_LITTLE_ENDIAN;
      } elseif ($n == strrev($s)) {
        $this->_byteorder= BIFF_BIG_ENDIAN;
      } else {
        throw(new lang::FormatException('Cannot determine byte order'));
      }
      
    }
    
    /**
     * Set version
     *
     * @param   int version
     */
    public function setVersion($version) {
      $this->version= $version;
    }
    
    /**
     * Retrieve version
     *
     * @return  int version
     */
    public function getVersion() {
      return $this->version;
    }
    
    /**
     * Prepend binary data
     *
     * @param   string d
     */
    protected function _prepend($d) {
      if (strlen($d) > $this->_limit) {
        $d= $this->_cont($d);
      }
      $this->_data= $d.$this->_data;
      $this->_datasize+= strlen($d);
    }
    
    /**
     * Prepend binary data
     *
     * @param   string d
     */
    protected function _append($d) {
      if (strlen($d) > $this->_limit) {
        $d= $this->_addCont($d);
      }
      $this->_data= $this->_data.$d;
      $this->_datasize+= strlen($d);
    }

    /**
     * Writes BOF record
     *
     * @param   int type one of the BOF_TYPE_* constants
     */
    protected function _bof($type) {
      $this->_prepend(
        pack('vv', 0x0809, 0x0008).
        pack('vvvv', $this->version, $type, 0x096C, 0x07C9)
      );
    }
    
    /**
     * Writes EOF record
     *
     */
    protected function _eof() {
      $this->_append(pack('vv', 0x000A, 0x0000));
    }
    
    /**
     * Insert continue records
     *
     * @param   string data
     * @return  string data
     */
    protected function _cont($d) {
      $h= pack('vv', 0x003C, $this->_limit);
      $t= substr($d, 0, 2).pack('v', $this->_limit- 4).substr($d, 4, $this->_limit- 4);
      for ($i= $this->_limit, $len= strlen($d); $i < $len; $i+= $this->_limit) {
        $t.= $h.substr($d, $i, $this->_limit);
      }
      return $t.pack('vv', 0x003C, strlen($d)- $i).substr($d, $i, strlen($d)- $i);
    }
    
    /**
     * Write to a stream
     *
     * @see     xp://io.Stream#write
     * @param   &io.Stream stream
     * @return  &io.Stream stream passed in
     * @throws  io.IOException
     */
    public function write($stream) {
      $stream->open(FILE_MODE_WRITE);
      $stream->write($this->_data);
      $stream->close();
      return $stream;
    }
  }
?>
