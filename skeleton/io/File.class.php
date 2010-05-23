<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  // Mode constants for open() method
  define('FILE_MODE_READ',      'rb');          // Read
  define('FILE_MODE_READWRITE', 'rb+');         // Read/Write
  define('FILE_MODE_WRITE',     'wb');          // Write
  define('FILE_MODE_REWRITE',   'wb+');         // Read/Write, truncate on open
  define('FILE_MODE_APPEND',    'ab');          // Append (Read-only)
  define('FILE_MODE_READAPPEND','ab+');         // Append (Read/Write)
  
  uses(
    'io.Stream',
    'io.IOException',
    'io.FileNotFoundException',
    'io.streams.FileInputStream', 
    'io.streams.FileOutputStream'
  );
    
  /**
   * Instances of the file class serve as an opaque handle to the underlying machine-
   * specific structure representing an open file.
   * 
   * @test     xp://net.xp_framework.unittest.io.FileTest
   * @purpose  Represent a file
   */
  class File extends Stream {
    public 
      $uri=         '', 
      $filename=    '',
      $path=        '',
      $extension=   '',
      $mode=        FILE_MODE_READ;
    
    public 
      $_fd= NULL;
    
    /**
     * Constructor. Supports creation via one of the following ways:
     * <code>
     *   // via resource
     *   $f= new File(fopen('lang/Type.class.php', FILE_MODE_READ));
     *
     *   // via filename
     *   $f= new File('lang/Type.class.php');
     *  
     *   // via dir- and filename
     *   $f->new File('lang', 'Type.class.php');
     *
     *   // via folder and filename
     *   $f->new File(new Folder('lang'), 'Type.class.php');
     * </code>
     *
     * @param   var base either a resource or a filename
     * @param   string uri
     */
    public function __construct($base, $uri= NULL) {
      if (is_resource($base)) {
        $this->uri= NULL;
        $this->_fd= $base;
      } else if (NULL === $uri) {
        $this->setURI($base);
      } else if (is('io.Folder', $base)) {
        $this->setURI($base->getURI().$uri);
      } else {
        $this->setURI(rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$uri);
      }
    }

    /**
     * Retrieve input stream
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return new FileInputStream($this);
    }

    /**
     * Retrieve output stream
     *
     * @return  io.streams.OutputStream
     */
    public function getOutputStream() {
      return new FileOutputStream($this);
    }
    
    /**
     * Retrieve internal file handle
     *
     * @return  resource
     */
    public function getHandle() {
      return $this->_fd;
    }
    
    /**
     * Returns the URI of the file
     *
     * @return string uri
     */
    public function getURI() {
      return $this->uri;
    }
    
    /**
     * Returns the filename of the file
     *
     * @return string filename
     */
    public function getFileName() {
      return $this->filename;
    }

    /**
     * Get Path
     *
     * @return  string
     */
    public function getPath() {
      return $this->path;
    }

    /**
     * Get Extension
     *
     * @return  string
     */
    public function getExtension() {
      return $this->extension;
    }

    /**
     * Set this file's URI
     *
     * @param   string uri
     * @throws  lang.IllegalArgumentException in case an invalid file name was given
     */
    public function setURI($uri) {
      static $allowed= array('xar://*', 'php://stderr', 'php://stdout', 'php://stdin', 'res://*');

      $uri= (string)$uri;
      if (0 === strlen($uri) || FALSE !== strpos($uri, "\0")) {
        throw new IllegalArgumentException('Invalid filename "'.addcslashes($uri, "\0..\17").'"');
      } else if (FALSE !== strpos($uri, '://')) {
        if (!in_array($uri, $allowed) && !in_array(substr($uri, 0, 6).'*', $allowed)) {
          throw new IllegalArgumentException('Invalid scheme URI "'.$uri.'"');
        }
        $this->path= NULL;
        $this->extension= NULL;
        $this->filename= $this->uri= $uri;
      } else {
        $this->uri= realpath($uri);

        // Bug in real_path when file does not exist
        if ('' == $this->uri && $uri != $this->uri) $this->uri= $uri;

        with ($pathinfo= pathinfo($this->uri)); {
          $this->path= $pathinfo['dirname'];
          $this->filename= $pathinfo['basename'];
          $this->extension= isset($pathinfo['extension']) ? $pathinfo['extension'] : NULL;
        }
      }
    }

    /**
     * Open the file
     *
     * @param   string mode one of the FILE_MODE_* constants
     * @return  bool TRUE if file could be opened
     * @throws  io.FileNotFoundException in case the file is not found
     * @throws  io.IOException in case the file cannot be opened (e.g., lacking permissions)
     */
    public function open($mode= FILE_MODE_READ) {
      $this->mode= $mode;
      if (
        0 != strncmp('php://', $this->uri, 6) &&
        (FILE_MODE_READ == $mode) && 
        (!$this->exists())
      ) throw new FileNotFoundException($this->uri);
      
      $this->_fd= fopen($this->uri, $this->mode);
      if (!$this->_fd) {
        $e= new IOException('Cannot open '.$this->uri.' mode '.$this->mode);
        xp::gc(__FILE__);
        throw $e;
      }
            
      return TRUE;
    }
    
    /**
     * Returns whether this file is open
     *
     * @return  bool TRUE, if the file is open
     */
    public function isOpen() {
      return is_resource($this->_fd);
    }
    
    /**
     * Returns whether this file eixtss
     *
     * @return  bool TRUE in case the file exists
     */
    public function exists() {
      return file_exists($this->uri);
    }
    
    /**
     * Retrieve the file's size in bytes
     *
     * @return  int size filesize in bytes
     * @throws  io.IOException in case of an error
     */
    public function size() {
      if (FALSE === ($size= filesize($this->uri))) {
        $e= new IOException('Cannot get filesize for '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $size;
    }
    
    /**
     * Truncate the file to the specified length
     *
     * @param   bool TRUE if method succeeded
     * @throws  io.IOException in case of an error
     */
    public function truncate($size= 0) {
      if (FALSE === ($return= ftruncate($this->_fd, $size))) {
        $e= new IOException('Cannot truncate file '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $return;
    }

    /**
     * Retrieve last access time
     *
     * Note: 
     * The atime of a file is supposed to change whenever the data blocks of a file 
     * are being read. This can be costly performancewise when an application 
     * regularly accesses a very large number of files or directories. Some Unix 
     * filesystems can be mounted with atime updates disabled to increase the 
     * performance of such applications; USENET news spools are a common example. 
     * On such filesystems this function will be useless. 
     *
     * @return  int The date the file was last accessed as a unix-timestamp
     * @throws  io.IOException in case of an error
     */
    public function lastAccessed() {
      if (FALSE === ($atime= fileatime($this->uri))) {
        $e= new IOException('Cannot get atime for '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $atime;
    }
    
    /**
     * Retrieve last modification time
     *
     * @return  int The date the file was last modified as a unix-timestamp
     * @throws  io.IOException in case of an error
     */
    public function lastModified() {
      if (FALSE === ($mtime= filemtime($this->uri))) {
        $e= new IOException('Cannot get mtime for '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $mtime;
    }
    
    /**
     * Set last modification time
     *
     * @param   int time default -1 Unix-timestamp
     * @return  bool success
     * @throws  io.IOException in case of an error
     */
    public function touch($time= -1) {
      if (-1 == $time) $time= time();
      if (FALSE === touch($this->uri, $time)) {
        $e= new IOException('Cannot set mtime for '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return TRUE;
    }

    /**
     * Retrieve when the file was created
     *
     * @return  int The date the file was created as a unix-timestamp
     * @throws  io.IOException in case of an error
     */
    public function createdAt() {
      if (FALSE === ($mtime= filectime($this->uri))) {
        $e= new IOException('Cannot get mtime for '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $mtime;
    }

    /**
     * Read one line and chop off trailing CR and LF characters
     *
     * Returns a string of up to length - 1 bytes read from the file. 
     * Reading ends when length - 1 bytes have been read, on a newline (which is 
     * included in the return value), or on EOF (whichever comes first). 
     *
     * @param   int bytes default 4096 Max. amount of bytes to be read
     * @return  string Data read
     * @throws  io.IOException in case of an error
     */
    public function readLine($bytes= 4096) {
      return chop($this->gets($bytes));
    }
    
    /**
     * Read one char
     *
     * @return  string the character read
     * @throws  io.IOException in case of an error
     */
    public function readChar() {
      if (FALSE === ($result= fgetc($this->_fd)) && !feof($this->_fd)) {
        $e= new IOException('Cannot read 1 byte from '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $result;
    }

    /**
     * Read a line
     *
     * This function is identical to readLine except that trailing CR and LF characters
     * will be included in its return value
     *
     * @param   int bytes default 4096 Max. amount of bytes to read
     * @return  string Data read
     * @throws  io.IOException in case of an error
     */
    public function gets($bytes= 4096) {
      if (0 === $bytes) return '';
      if (FALSE === ($result= fgets($this->_fd, $bytes)) && !feof($this->_fd)) {
        $e= new IOException('Cannot read '.$bytes.' bytes from '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $result;
    }

    /**
     * Read (binary-safe)
     *
     * @param   int bytes default 4096 Max. amount of bytes to read
     * @return  string Data read
     * @throws  io.IOException in case of an error
     */
    public function read($bytes= 4096) {
      if (0 === $bytes) return '';
      if (FALSE === ($result= fread($this->_fd, $bytes)) && !feof($this->_fd)) {
        $e= new IOException('Cannot read '.$bytes.' bytes from '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $result;
    }

    /**
     * Write
     *
     * @param   string string data to write
     * @return  int number of bytes written
     * @throws  io.IOException in case of an error
     */
    public function write($string) {
      if (!$this->_fd || FALSE === ($result= fwrite($this->_fd, $string))) {
        $e= new IOException('Cannot write '.strlen($string).' bytes to '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $result;
    }

    /**
     * Write a line and append a LF (\n) character
     *
     * @param   string string data default '' to write
     * @return  int number of bytes written
     * @throws  io.IOException in case of an error
     */
    public function writeLine($string= '') {
      if (!$this->_fd || FALSE === ($result= fwrite($this->_fd, $string."\n"))) {
        $e= new IOException('Cannot write '.(strlen($string)+ 1).' bytes to '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
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
     * @return  bool TRUE when the end of the file is reached
     * @throws  io.IOException in case of an error (e.g., the file's not been opened)
     */
    public function eof() {
      $result= feof($this->_fd);
      if (xp::errorAt(__FILE__, __LINE__ - 1)) {
        $e= new IOException('Cannot determine eof of '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return $result;
    }
    
    /**
     * Sets the file position indicator for fp to the beginning of the 
     * file stream. 
     * 
     * This function is identical to a call of $f->seek(0, SEEK_SET)
     *
     * @return  bool TRUE if rewind suceeded
     * @throws  io.IOException in case of an error
     */
    public function rewind() {
      if (FALSE === ($result= rewind($this->_fd))) {
        $e= new IOException('Cannot rewind file pointer');
        xp::gc(__FILE__);
        throw $e;
      }
      return TRUE;
    }
    
    /**
     * Move file pointer to a new position
     *
     * @param   int position default 0 The new position
     * @param   int mode default SEEK_SET 
     * @see     php://fseek
     * @throws  io.IOException in case of an error
     * @return  bool success
     */
    public function seek($position= 0, $mode= SEEK_SET) {
      if (0 != ($result= fseek($this->_fd, $position, $mode))) {
        $e= new IOException('Seek error, position '.$position.' in mode '.$mode);
        xp::gc(__FILE__);
        throw $e;
      }
      return TRUE;
    }
    
    /**
     * Retrieve file pointer position
     *
     * @return  int position
     * @throws  io.IOException in case of an error
     */
    public function tell() {
      if (FALSE === ($result= ftell($this->_fd))) {
        $e= new IOException('Cannot retrieve file pointer\'s position');
        xp::gc(__FILE__);
        throw $e;
      }
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
     * @param   int op operation (one of the predefined LOCK_* constants)
     * @throws  io.IOException in case of an error
     * @return  bool success
     * @see     php://flock
     */
    protected function _lock($mode) {
      if (FALSE === flock($this->_fd, $mode)) {
        $os= '';
        foreach (array(
          LOCK_NB   => 'LOCK_NB',
          LOCK_UN   => 'LOCK_UN', 
          LOCK_EX   => 'LOCK_EX', 
          LOCK_SH   => 'LOCK_SH' 
        ) as $o => $s) {
          if ($mode >= $o) { 
            $os.= ' | '.$s;
            $mode-= $o;
          }
        }
        $e= new IOException('Cannot lock file '.$this->uri.' w/ '.substr($os, 3));
        xp::gc(__FILE__);
        throw $e;
      }
      
      return TRUE;
    }
    
    /**
     * Acquire a shared lock (reader)
     *
     * @param   bool block default FALSE
     * @see     xp://io.File#_lock
     * @return  bool success
     */
    public function lockShared($block= FALSE) {
      return $this->_lock(LOCK_SH + ($block ? 0 : LOCK_NB));
    }
    
    /**
     * Acquire an exclusive lock (writer)
     *
     * @param   bool block default FALSE
     * @see     xp://io.File#_lock
     * @return  bool success
     */
    public function lockExclusive($block= FALSE) {
      return $this->_lock(LOCK_EX + ($block ? 0 : LOCK_NB));
    }
    
    /**
     * Release a lock (shared or exclusive)
     *
     * @see     xp://io.File#_lock
     * @return  bool success
     */
    public function unLock() {
      return $this->_lock(LOCK_UN);
    }

    /**
     * Close this file
     *
     * @return  bool success
     * @throws  io.IOException if close fails
     */
    public function close() {
      if (!is_resource($this->_fd)) {
        throw new IOException('Cannot close non-opened file '.$this->uri);
      }
      if (FALSE === fclose($this->_fd)) {
        $e= new IOException('Cannot close file '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
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
     * @return  bool success
     * @throws  io.IOException in case of an error (e.g., lack of permissions)
     * @throws  lang.IllegalStateException in case the file is still open
     */
    public function unlink() {
      if (is_resource($this->_fd)) {
        throw new IllegalStateException('File still open');
      }
      if (FALSE === unlink($this->uri)) {
        $e= new IOException('Cannot delete file '.$this->uri);
        xp::gc(__FILE__);
        throw $e;
      }
      return TRUE;
    }
    
    /**
     * Move this file
     *
     * Warning: Open files cannot be moved. Use the close() method to
     * close the file first
     *
     * @param   string target where to move the file to
     * @return  bool success
     * @throws  io.IOException in case of an error (e.g., lack of permissions)
     * @throws  lang.IllegalStateException in case the file is still open
     */
    public function move($target) {
      if (is_resource($this->_fd)) {
        throw new IllegalStateException('File still open');
      }
      if (FALSE === rename($this->uri, $target)) {
        $e= new IOException('Cannot move file '.$this->uri.' to '.$target);
        xp::gc(__FILE__);
        throw $e;
      }
      
      $this->setURI($target);
      return TRUE;
    }
    
    /**
     * Copy this file
     *
     * Warning: Open files cannot be copied. Use the close() method to
     * close the file first
     *
     * @param   string target where to copy the file to
     * @return  bool success
     * @throws  io.IOException in case of an error (e.g., lack of permissions)
     * @throws  lang.IllegalStateException in case the file is still open
     */
    public function copy($target) {
      if (is_resource($this->_fd)) {
        throw new IllegalStateException('File still open');
      }
      
      if (FALSE === copy($this->uri, $target)) {
        $e= new IOException('Cannot copy file '.$this->uri.' to '.$target);
        xp::gc(__FILE__);
        throw $e;
      }
      return TRUE;
    }
    
    /**
     * Change permissions for the file
     *
     * @see     php://chmod
     * @param   var mode
     * @return  bool success
     */
    public function setPermissions($mode) {
      return chmod($this->uri, $mode);
    }
    
    /**
     * Get permission mask of the file
     *
     * @see     php://stat
     * @return  int
     */
    public function getPermissions() {
      $stat= stat($this->uri);
      return $stat['mode'];
    }

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(uri= %s, mode= %s)',
        $this->getClassName(),
        $this->uri,
        $this->mode
      );
    }
  }
?>
