<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  // Mode constants for open() method
  define('FILE_MODE_READ',      'r');          // Read
  define('FILE_MODE_READWRITE', 'r+');         // Read/Write
  define('FILE_MODE_WRITE',     'w');          // Write
  define('FILE_MODE_REWRITE',   'w+');         // Read/Write, truncate on open
  define('FILE_MODE_APPEND',    'a');          // Append (Read-only)
  define('FILE_MODE_READAPPEND','a+');         // Append (Read/Write)
  
  // Define default filehandles
  if (!defined('STDIN'))  define('STDIN',      fopen ('php://stdin',  'r'));          
  if (!defined('STDOUT')) define('STDOUT',     fopen ('php://stdout', 'w'));          
  if (!defined('STDERR')) define('STDERR',     fopen ('php://stderr', 'w'));         
  
  uses(
    'io.IOException',
    'io.FileNotFoundException'
  );
    
  /**
   * Represents a file
   *
   * Instances of the file class serve as an opaque handle to the underlying machine-
   * specific structure representing an open file.
   * 
   */
  class File extends Object {
    var 
      $uri=         '', 
      $filename=    '',
      $path=        '',
      $extension=   '',
      $mode=        FILE_MODE_READ;
    
    var 
      $_fd= NULL;
    
    /**
     * Constructor
     *
     * @param  string filename The filename, e.g. /etc/hosts
     */
    function __construct($filename) {
      $this->setURI($filename);
      parent::__construct();
    }

    /**
     * URI setzen. Definiert andere Attribute wie path, filename und extension
     *
     * @access  private
     * @param   string uri Die URI
     */
    function setURI($uri) {
    
      // PHP-Scheme
      if ('php://' == substr($uri, 0, 6)) {
        $this->path= NULL;
        $this->extension= NULL;
        $this->filename= $this->uri= $uri;
        return;
      }
      
      $this->uri= realpath($uri);
      
      // Bug in real_path (wenn Datei nicht existiert, ist die Rückgabe ein leerer String!)
      if ('' == $this->uri && $uri != $this->uri) $this->uri= $uri;
      
      $this->path= dirname($uri);
      $this->filename= basename($uri);
      $this->extension= NULL;
      if (preg_match('/\.(.+)$/', $this->filename, $regs)) $this->extension= $regs[1];
    }

    /**
     * Open the file
     *
     * @access  public
     * @param   string mode one of the FILE_MODE_* constants
     * @throws  FileNotFoundException in case the file is not found
     * @throws  IOException in case the file cannot be opened (e.g., lacking permissions)
     */
    function open($mode= FILE_MODE_READ) {
      $this->mode= $mode;
      if (
        ('php://' != substr($this->uri, 0, 6)) &&
        ($mode== FILE_MODE_READ) && 
        (!$this->exists())
      ) return throw(new FileNotFoundException($this->uri));
      
      // Öffnen
      $this->_fd= fopen($this->uri, $this->mode);
      if (!$this->_fd) return throw(new IOException('cannot open '.$this->uri.' mode '.$this->mode));
      
      return TRUE;
    }
	
    /**
     * Returns whether this file is open
     *
     * @access  public
     * @return  bool TRUE, when the file is open
     */
	function isOpen() {
	  return $this->_fd;
	}
    
    /**
     * Returns whether this file eixtss
     *
     * @access  public
     * @return  bool TRUE in case the file exists
     */
    function exists() {
      return file_exists($this->uri);
    }
    
    /**
     * Retreive the file's size in bytes
     *
     * @access  public
     * @return  int size filesize in bytes
     * @throws  IOException in case of an error
     */
    function size() {
      $size= filesize($this->uri);
      if (FALSE === $size) return throw(new IOException('cannot get filesize for '.$this->uri));
      return $size;
    }
    
    /**
     * Truncate the file to the specified length
     *
     * @access  public
     * @param   int size default 0 New size in bytes
     * @throws  IOException in case of an error
     */
    function truncate($size= 0) {
      $return= ftruncate($this->_fd, $size);
      if (FALSE === $return) return throw(new IOException('cannot truncate file '.$this->uri));
      return $return;
    }

    /**
     * Retreive last access time
     *
     * Note: 
     * The atime of a file is supposed to change whenever the data blocks of a file 
     * are being read. This can be costly performancewise when an application 
     * regularly accesses a very large number of files or directories. Some Unix 
     * filesystems can be mounted with atime updates disabled to increase the 
     * performance of such applications; USENET news spools are a common example. 
     * On such filesystems this function will be useless. 
     *
     * @access  public
     * @return  int The date the file was last accessed as a unix-timestamp
     * @throws  IOException in case of an error
     */
    function lastAccessed() {
      $atime= fileatime($this->uri);
      if (FALSE === $atime) return throw(new IOException('cannot get atime for '.$this->uri));
      return $atime;
    }
    
    /**
     * Retreive last modification time
     *
     * @access  public
     * @return  int The date the file was last modified as a unix-timestamp
     * @throws  IOException in case of an error
     */
    function lastModified() {
      $mtime= fileatime($this->uri);
      if (FALSE === $mtime) return throw(new IOException('cannot get mtime for '.$this->uri));
      return $mtime;
    }
    
    /**
     * Set last modification time
     *
     * @access  public
     * @param   int time default -1 Unix-timestamp
     * @return  bool success
     * @throws  IOException in case of an error
     */
    function touch($time= -1) {
      if (-1 == $time) $time= time();
      if (FALSE === touch($this->uri, $time)) {
        return throw(new IOException('cannot set mtime for '.$this->uri));
      }
      return TRUE;
    }

    /**
     * Retreive when the file was created
     *
     * @access  public
     * @return  int The date the file was created as a unix-timestamp
     * @throws  IOException in case of an error
     */
    function createdAt() {
      if (!isset($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $mtime= filectime($this->uri);
      if (FALSE === $mtime) return throw(new IOException('cannot get mtime for '.$this->uri));
      return $mtime;
    }

    /**
     * Read one line and chop off trailing CR and LF characters
     *
     * Returns a string of up to length - 1 bytes read from the file. 
     * Reading ends when length - 1 bytes have been read, on a newline (which is 
     * included in the return value), or on EOF (whichever comes first). 
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     * @throws  IOException in case of an error
     */
    function readLine($bytes= 4096) {
      $result= chop(fgets($this->_fd, $bytes));
      if (is_error() && $result === FALSE) {
        return throw(new IOException('readLine() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Read one char
     *
     * @access  public
     * @return  char the character read
     * @throws  IOException in case of an error
     */
    function readChar() {
      $result= fgetc($this->_fd);
      if (is_error() && $result === FALSE) {
        return throw(new IOException('readChar() cannot read 1 byte from '.$this->uri));
      }
      return $result;
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
     * @throws  IOException in case of an error
     */
    function gets($bytes= 4096) {
      $result= fgets($this->_fd, $bytes);
      if (is_error() && $result === FALSE) {
        return throw(new IOException('gets() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Read (binary-safe)
     *
     * @access  public
     * @param   int bytes default 4096 Max. ammount of bytes to be read
     * @return  string Data read
     * @throws  IOException in case of an error
     */
    function read($bytes= 4096) {
      $result= fread($this->_fd, $bytes);
      if (is_error() && $result === FALSE) {
        return throw(new IOException('read() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Write
     *
     * @access  public
     * @param   string string data to write
     * @return  bool success
     * @throws  IOException in case of an error
     */
    function write($string) {
      $result= fwrite($this->_fd, $string);
      if (is_error() && $result === FALSE) {
        throw(new IOException('cannot write '.strlen($string).' bytes to '.$this->uri));
      }
      return $result;
    }

    /**
     * Write a line and append a LF (\n) character
     *
     * @access  public
     * @param   string string data to write
     * @return  bool success
     * @throws  IOException in case of an error
     */
    function writeLine($string) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= fputs($this->_fd, $string."\n");
      if (is_error() && $result === FALSE) {
        throw(new IOException('cannot write '.(strlen($string)+ 1).' bytes to '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Returns whether the file pointer is at the end of the file
     *
     * Hint:
     * Use isOpen() to check if the file is open
     *
     * @see     php://feof
     * @access  public
     * @return  bool TRUE when the end of the file is reached
     * @throws  IOException in case of an error (e.g., the file's not been opened)
     */
    function eof() {
      $result= feof($this->_fd);
      if ($result && is_error()) {
        throw(new IOException('cannot determine eof of '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Sets the file position indicator for fp to the beginning of the 
     * file stream. 
     * 
     * This function is identical to a call of $f->seek(0, SEEK_SET)
     *
     * @access  public
     * @throws  IOException in case of an error
     */
    function rewind() {
      if (!($result= rewind($this->_fd))) {
        return throw(new IOException('cannot rewind file pointer'));
      }
      return TRUE;
    }
    
    /**
     * Move file pointer to a new position
     *
     * @access  public
     * @param   int position default 0 The new position
     * @param   int mode default SEEK_SET 
     * @see     php://fseek
     * @throws  IOException in case of an error
     * @return  bool success
     */
    function seek($position= 0, $mode= SEEK_SET) {
      $result= fseek($this->_fd, $position, $mode);
      if ($result != 0) return throw(new IOException('seek error, position '.$position.' in mode '.$mode));
      return TRUE;
    }
    
    /**
     * Retreive file pointer position
     *
     * @access  public
     * @throws  IOException in case of an error
     * @return  int position
     */
    function tell() {
      $result= ftell($this->_fd);
      if (FALSE === $result) return throw(new IOException('cannot retreive file pointer\'s position'));
      return $result;
    }

    /**
     * Private wrapper function for locking
     *
     * Warning:
     * flock() will not work on NFS and many other networked file systems. Check your 
     * operating system documentation for more details. On some operating systems flock() 
     * is implemented at the process level. When using a multithreaded server API like 
     * ISAPI you may not be able to rely on flock() to protect files against other PHP 
     * scripts running in parallel threads of the same server instance! flock() is not 
     * supported on antiquated filesystems like FAT and its derivates and will therefore 
     * always return FALSE under this environments (this is especially true for Windows 98 
     * users). 
     *
     * The optional second argument is set to TRUE if the lock would block (EWOULDBLOCK 
     * errno condition).
     *
     * @access  private
     * @param   int Operation
     * @param   int Block
     * @throws  IOException in case of an error
     * @return  boolean success
     * @see     php://flock
     */
    function _lock($op, $block= NULL) {
      $result= flock($this->_fd, $op, $block);
      if (FALSE === $result) {
        $os= '';
        foreach (array(
          LOCK_SH   => 'LOCK_SH', 
          LOCK_EX   => 'LOCK_EX', 
          LOCK_UN   => 'LOCK_UN', 
          LOCK_NB   => 'LOCK_NB'
        ) as $o => $s) {
          if ($op & $o) $os.= ' | '.$s;
        }
        return throw(new IOException('cannot lock file '.$this->uri.' w/ '.substr($os, 3)));
      }
      return $result;
    }
    
    /**
     * Acquire a shared lock (reader)
     *
     * @access  public
     * @see     File#_lock
     */
    function lockShared($lock= NULL) {
      return $this->_lock(LOCK_SH, $lock);
    }
    
    /**
     * Acquire an exclusive lock (writer)
     *
     * @access  public
     * @see     File#_lock
     */
    function lockExclusive($lock= NULL) {
      return $this->_lock(LOCK_EX, $lock);
    }
    
    /**
     * Release a lock (shared or exclusive)
     *
     * @access  public
     * @see     File#_lock
     */
    function unLock() {
      return $this->_lock(LOCK_UN);
    }

    /**
     * Close this file
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      if (FALSE === fclose($this->_fd)) {
        return throw(new IOException('cannot close file '.$this->uri));
      }
      
      $this->_fd= NULL;
      return TRUE;
    }
    
    /**
     * Delete this file
     *
     * Warning: Open files cannot be deleted. Use the close() method to
     * close the file first
     *
     * @access  public
     * @return  bool success
     * @throws  IOException in case of an error (e.g., lack of permissions)
     * @throws  IllegalStateException in case the file is still open
     */
    function unlink() {
      if (is_resource($this->_fd)) {
        return throw(new IllegalStateException('file still open'));
      }
      
      if (FALSE === unlink($this->_fd)) {
        return throw(new IOException('cannot delete file '.$this->uri));
      }
      return TRUE;
    }
    
    /**
     * Move this file
     *
     * Warning: Open files cannot be moved. Use the close() method to
     * close the file first
     *
     * @access  public
     * @return  bool success
     * @throws  IOException in case of an error (e.g., lack of permissions)
     * @throws  IllegalStateException in case the file is still open
     */
    function move($target) {
      if (is_resource($this->_fd)) {
        return throw(new IllegalStateException('file still open'));
      }
      
      if (FALSE === rename($this->uri, $target)) {
        return throw(new IOException('cannot move file '.$this->uri.' to '.$target));
      }
      return TRUE;
    }
    
    /**
     * Copy this file
     *
     * Warning: Open files cannot be copied. Use the close() method to
     * close the file first
     *
     * @access  public
     * @return  bool success
     * @throws  IOException in case of an error (e.g., lack of permissions)
     * @throws  IllegalStateException in case the file is still open
     */
    function copy($target) {
      if (is_resource($this->_fd)) {
        return throw(new IllegalStateException('file still open'));
      }
      
      if (FALSE === copy($this->uri, $target)) {
        return throw(new IOException('cannot move file '.$this->uri.' to '.$target));
      }
      return TRUE;
    }
  }
?>
