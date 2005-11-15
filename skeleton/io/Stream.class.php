<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  // Mode constants for open() method
  define('STREAM_MODE_READ',      'r');          // Read
  define('STREAM_MODE_READWRITE', 'r+');         // Read/Write
  define('STREAM_MODE_WRITE',     'w');          // Write
  define('STREAM_MODE_REWRITE',   'w+');         // Read/Write, truncate on open
  define('STREAM_MODE_APPEND',    'a');          // Append (Read-only)
  define('STREAM_MODE_READAPPEND','a+');         // Append (Read/Write)
  
  define('STREAM_READ',  0x0001);
  define('STREAM_WRITE', 0x0002);

  /**
   * Stream
   * 
   * @test     xp://net.xp_framework.unittest.io.StreamTest
   * @purpose  Represent a generic stream
   */
  class Stream extends Object {
    var
      $buffer   = '',
      $flags    = 0,
      $offset   = 0;
      
    /**
     * Open the stream
     *
     * @access  public
     * @param   string mode default STREAM_MODE_READ one of the STREAM_MODE_* constants
     */
    function open($mode= STREAM_MODE_READ) {
      $this->offset= 0;

      switch ($mode) {
        case STREAM_MODE_READWRITE:
          $this->flags= STREAM_WRITE;
          // break missing intentionally
          
        case STREAM_MODE_READ:
          $this->flags|= STREAM_READ;
          break;

        case STREAM_MODE_REWRITE:
          $this->buffer= '';
          // break missing intentionally
          
        case STREAM_MODE_WRITE:
          $this->flags= STREAM_WRITE;
          break;

        case STREAM_MODE_READAPPEND:
          $this->flags= STREAM_READ;
          // break missing intentionally
          
        case STREAM_MODE_APPEND:
          $this->flags|= STREAM_WRITE;
          $this->offset= strlen($this->buffer);
          break;
          
      }
    }
    
    /**
     * Returns whether this stream is open
     *
     * @access  public
     * @return  bool TRUE, when the stream is open
     */
    function isOpen() {
      return $this->flags != 0;
    }
    
    /**
     * Retrieve the stream's size in bytes
     *
     * @access  public
     * @return  int size streamsize in bytes
     */
    function size() {
      return strlen($this->buffer);
    }
    
    /**
     * Truncate the stream to the specified length
     *
     * @access  public
     * @param   int size default 0
     * @return  bool
     */
    function truncate($size= 0) {
      if (strlen($this->buffer) > $size) {
        $this->buffer= substr($this->buffer, 0, $size);
        
        // If position is in truncated area, rewind it as far as needed
        if ($this->offset > $size) $this->offset= strlen($this->buffer);
        return TRUE;
      }
      
      return FALSE;
    }

    /**
     * Read one line and chop off trailing CR and LF characters
     *
     * Returns a string of up to length - 1 bytes read from the stream. 
     * Reading ends when length - 1 bytes have been read, on a newline (which is 
     * included in the return value), or on EOF (whichever comes first). 
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    function readLine($bytes= 4096) {
      return chop($this->gets($bytes));
    }
    
    /**
     * Read one char
     *
     * @access  public
     * @return  char the character read
     */
    function readChar() {
      return substr($this->buffer, $this->offset++, 1);
    }

    /**
     * Read a line
     *
     * This function is identical to readLine except that trailing CR and LF characters
     * will be included in its return value
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    function gets($bytes= 4096) {
      if ($this->eof()) return '';
      if (FALSE === ($p= strpos($this->buffer, "\n", $this->offset))) $p= $bytes;
      $l= min($p + 1 - $this->offset, $bytes);
      $this->offset+= $l;
      return substr($this->buffer, $this->offset - $l, $l);
    }

    /**
     * Read (binary-safe)
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     */
    function read($bytes= 4096) {
      if ($this->eof()) return '';
      if (FALSE === ($data= substr($this->buffer, $this->offset, $bytes))) {
        return throw(new IOException('Cannot read '.$bytes.' bytes from stream.'));
      }
      
      $this->offset+= strlen($data);
      return $data;
    }

    /**
     * Write
     *
     * @access  public
     * @param   string string data to write
     * @return  int number of bytes written
     */
    function write($string) {
    
      // Handle faster common case where we append to the end
      if ($this->offset == strlen($this->buffer)) {
        $this->buffer.= $string;
        $this->offset+= ($l= strlen($string));
        return $l;
      }
      
      // Now handle overwrite of stream data
      $this->buffer= (
        substr($this->buffer, 0, $this->offset).
        $string.
        substr($this->buffer, $this->offset+ strlen($string))
      );
      $this->offset+= ($l= strlen ($string));
      return $l;
    }

    /**
     * Write a line and append a LF (\n) character
     *
     * @access  public
     * @param   string string default '' data to write
     * @return  int number of bytes written
     */
    function writeLine($string= '') {
      return $this->write($string."\n");
    }
    
    /**
     * Returns whether the stream pointer is at the end of the stream
     *
     * Hint:
     * Use isOpen() to check if the stream is open
     *
     * @access  public
     * @return  bool TRUE when the end of the stream is reached
     */
    function eof() {
      return $this->offset >= strlen($this->buffer);
    }
    
    /**
     * Sets the stream position indicator for fp to the beginning of the 
     * stream stream. 
     * 
     * This function is identical to a call of $f->seek(0, SEEK_SET)
     *
     * @access  public
     */
    function rewind() {
      $this->offset= 0;
    }
    
    /**
     * Move stream pointer to a new position. If the pointer exceeds the
     * actual buffer size, it is reset to the end of the buffer. This case
     * is not considered an error.
     *
     * @see     php://fseek
     * @access  public
     * @param   int position default 0 The new position
     * @param   int mode default SEEK_SET 
     * @return  bool success
     */
    function seek($position= 0, $mode= SEEK_SET) {
      switch ($mode) {
        case SEEK_SET: $this->offset= $position; break;
        case SEEK_CUR: $this->offset+= $position; break;
        case SEEK_END: $this->offset= strlen($this->buffer)+ $position; break;
      }
      
      // Assure, we don't exceed buffer size
      if ($this->offset > strlen($this->buffer)) $this->offset= strlen($this->buffer);
      
      return TRUE;
    }
    
    /**
     * Retrieve stream pointer position
     *
     * @access  public
     * @return  int position
     */
    function tell() {
      return $this->offset;
    }


    /**
     * Close this stream
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      $this->flags= 0;
      return TRUE;
    }
  }
?>
