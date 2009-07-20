<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.Stream');

  /**
   * Encapsulated / embedded stream
   *
   * @test      xp://net.xp_framework.unittest.io.EncapsedStreamTest
   * @see       xp://io.Stream
   * @purpose   Encapsulated stream
   */
  class EncapsedStream extends Stream {
    public
      $_super     = NULL,
      $_offset    = 0,
      $_size      = 0;

    /**
     * Constructor
     *
     * @param   io.Stream super parent stream
     * @param   int offset offset where encapsed stream starts in parent stream
     * @param   int size
     * @throws  lang.IllegalStateException when stream is not yet opened
     */
    public function __construct($super, $offset, $size) {
      if (!$super->isOpen()) throw(new IllegalStateException(
        'Super-stream must be opened in EncapsedStream'
      ));
      
      $this->_super= $super;
      $this->_offset= $offset;
      $this->_size= $size;
    }
    
    /**
     * Prepares the stream for the next operation (eg. moves the
     * pointer to the correct position).
     *
     */
    protected function _prepare() {
      $this->_super->seek($this->_offset + $this->offset);
    }
    
    /**
     * Keep track of moved stream pointers in the parent
     * stream.
     *
     * Should be used internally to correctly calculate the offset
     * for subsequent reads.
     *
     * @param   mixed arg
     * @return  mixed arg
     */
    protected function _track($arg) {
      $this->offset+= ($this->_super->tell()- ($this->_offset+ $this->offset));
      return $arg;
    }
    
    /**
     * Open the stream. For EncapsedStream only reading is supported
     *
     * @param   string mode default STREAM_MODE_READ one of the STREAM_MODE_* constants
     */
    public function open($mode= STREAM_MODE_READ) {
      if (STREAM_MODE_READ !== $mode) throw(new IllegalAccessException(
        'EncapsedStream only supports reading but writing operation requested.'
      ));
    }
    
    /**
     * Returns whether this stream is open
     *
     * @return  bool TRUE, when the stream is open
     */
    public function isOpen() {
      return $this->_super->isOpen();
    }
    
    /**
     * Retrieve the stream's size in bytes
     *
     * @return  int size streamsize in bytes
     */
    public function size() {
      return $this->_size;
    }
    
    /**
     * Truncate the stream to the specified length
     *
     * @param   int size default 0
     * @return  bool
     */
    public function truncate($size= 0) {
      raise('lang.MethodNotImplementedException', 'Truncation not supported');
    }
    
    /**
     * Read one line and chop off trailing CR and LF characters
     *
     * Returns a string of up to length - 1 bytes read from the stream. 
     * Reading ends when length - 1 bytes have been read, on a newline (which is 
     * included in the return value), or on EOF (whichever comes first). 
     *
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    public function readLine($bytes= 4096) {
      $this->_prepare();
      return $this->_track($this->_super->readLine(min($bytes, $this->_size- $this->offset)));
    }
    
    /**
     * Read one char
     *
     * @return  string the character read
     */
    public function readChar() {
      $this->_prepare();
      return $this->_track($this->_super->readChar());
    }
    
    /**
     * Read a line
     *
     * This function is identical to readLine except that trailing CR and LF characters
     * will be included in its return value
     *
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    public function gets($bytes= 4096) {
      $this->_prepare();
      return $this->_track($this->_super->gets(min($bytes, $this->_size- $this->offset)));
    }
    
    /**
     * Read (binary-safe)
     *
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    public function read($bytes= 4096) {
      $this->_prepare();
      return $this->_track($this->_super->read(min($bytes, $this->_size- $this->offset)));
    }
    
    /**
     * Write. No supported in EncapsedStream
     *
     * @param   string string data to write
     * @return  int number of bytes written
     */
    public function write($string) {
      raise('lang.MethodNotImplementedException', 'Writing not supported');
    }    

    /**
     * Write a line and append a LF (\n) character. Not supported in EncapsedStream
     *
     * @param   string string default '' data to write
     * @return  int number of bytes written
     */
    public function writeLine($string= '') {
      raise('lang.MethodNotImplementedException', 'Writing not supported');
    }
    
    /**
     * Returns whether the stream pointer is at the end of the stream
     *
     * @return  bool TRUE when the end of the stream is reached
     */
    public function eof() {
      return $this->offset >= $this->_size;
    }
    
    /**
     * Move stream pointer to a new position. If the pointer exceeds the
     * actual buffer size, it is reset to the end of the buffer. This case
     * is not considered an error.
     *
     * @see     php://fseek
     * @param   int position default 0 The new position
     * @param   int mode default SEEK_SET 
     * @return  bool success
     */
    public function seek($position= 0, $mode= SEEK_SET) {
      switch ($mode) {
        case SEEK_SET: $this->offset= min($this->_size, $position); break;
        case SEEK_CUR: $this->offset= min($this->_size, $this->offset+ $position); break;
        case SEEK_END: $this->offset= $this->_size; break;
      }
      
      return TRUE;
    }

    /**
     * Retrieve stream pointer position
     *
     * @return  int position
     */
    public function tell() {
      return $this->offset;
    }


    /**
     * Close this stream
     *
     * @return  bool success
     */
    public function close() {
      return TRUE;
    }
  }
?>
