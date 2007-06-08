<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('io.IOException');
    
  /**
   * Represents a Folder
   *
   * Usage:
   * <code>
   *   try(); {
   *     $d= new Folder('/etc/');
   *     while (FALSE !== ($entry= $d->getEntry())) {
   *       printf("%s/%s\n", $d->uri, $entry);
   *     }
   *     $d->close();
   *   } if (catch('IOException', $e)) {
   *     $e->printStackTrace();
   *   }
   * </code>
   */
  class Folder extends Object {
    public 
      $uri= '',
      $dirname= '',
      $path= '';
    
    public
      $_hdir= FALSE;
      
    /**
     * Constructor
     *
     * @param   string dirname the directory's name
     */
    public function __construct($dirname= NULL) {
      if (NULL != $dirname) $this->setURI($dirname);
      
    }
    
    /**
     * Destructor
     *
     */
    public function __destruct() {
      $this->close();
    }
    
    /**
     * Close directory
     *
     */
    public function close() {
      if (FALSE != $this->_hdir) $this->_hdir->close();
      $this->_hdir= FALSE;
    }

    /**
     * Set URI
     *
     * @param   string uri the complete path name
     */
    public function setURI($uri) {
      if (FALSE === ($this->uri= realpath($uri))) {

        // realpath returns FALSE if the URI does not exist
        $this->uri= $uri;
      }
      
      // Add trailing / (or \, or whatever else DIRECTORY_SEPARATOR is defined to)
      // if necessary
      $this->uri= rtrim($this->uri, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

      // Calculate path and name
      $this->path= dirname($uri);
      $this->dirname= basename($uri);
    }
    
    /**
     * Get URI
     *
     * @return  string uri of this folder
     */    
    public function getURI() {
      return $this->uri;
    }
    
    /**
     * Create this directory, recursively, if needed.
     *
     * @param   int permissions default 0700
     * @return  bool TRUE in case the creation succeeded or the directory already exists
     * @throws  io.IOException in case of an error
     */
    public function create($permissions= 0700) {
      if ('' == (string)$this->uri) {
        throw new IOException('Cannot create folder with empty name');
      }
      
      // Border-case: Folder already exists
      if (is_dir($this->uri)) return TRUE;

      $i= 0;
      $umask= umask(000);
      while (FALSE !== ($i= strpos($this->uri, DIRECTORY_SEPARATOR, $i))) {
        if (is_dir($d= substr($this->uri, 0, ++$i))) continue;
        if (FALSE === mkdir($d, $permissions)) {
          umask($umask);
          throw new IOException(sprintf('mkdir("%s", %d) failed', $d, $permissions));
        }
      }
      umask($umask);
      return TRUE;
    }
    
    /**
     * Delete this folder and all its subentries recursively
     * Warning: Stops at the first element that can't be deleted!
     *
     * @return  bool success
     * @throws  io.IOException in case one of the entries could'nt be deleted
     */
    public function unlink($uri= NULL) {
      if (NULL === $uri) $uri= $this->uri; // We also use this recursively
      
      if (FALSE === ($d= dir($uri))) {
        throw new IOException('Directory '.$uri.' does not exist');
      }
      
      while (FALSE !== ($e= $d->read())) {
        if ('.' == $e || '..' == $e) continue;
        
        $fn= $d->path.$e;
        if (!is_dir($fn)) {
          $ret= unlink($fn);
        } else {
          $ret= $this->unlink($fn.DIRECTORY_SEPARATOR);
        }
        if (FALSE === $ret) throw new IOException(sprintf('unlink of "%s" failed', $fn));
      }
      $d->close();

      if (FALSE === rmdir($uri)) throw new IOException(sprintf('unlink of "%s" failed', $uri));
      
      return TRUE;
    }

    /**
     * Move this directory
     *
     * Warning: Open directories cannot be moved. Use the close() method to
     * close the directory first
     *
     * @return  bool success
     * @throws  io.IOException in case of an error (e.g., lack of permissions)
     * @throws  lang.IllegalStateException in case the directory is still open
     */
    public function move($target) {
      if (is_resource($this->_hdir)) {
        throw new IllegalStateException('Directory still open');
      }
      if (FALSE === rename($this->uri, $target)) {
        throw new IOException('Cannot move directory '.$this->uri.' to '.$target);
      }
      return TRUE;
    }

    /**
     * Returns whether this directory exists
     *
     * @return  bool TRUE in case the directory exists
     */
    public function exists() {
      return is_dir($this->uri);
    }
    
    /**
     * Read through the contents of the directory, ommitting the entries "." and ".."
     *
     * @return  string entry directory entry (w/o path!), FALSE, if no more entries are left
     * @throws  io.IOException in case an error occurs
     */
    public function getEntry() {
      if (FALSE === $this->_hdir) {

        // Not open yet, try to open
        if (!is_object($this->_hdir= dir($this->uri))) {
          $this->_hdir= FALSE;
          throw new IOException('Cannot open directory "'.$this->uri.'"');
        }
      }
      
      while (FALSE !== ($entry= $this->_hdir->read())) {
        if ($entry != '.' && $entry != '..') return $entry;
      }
      return FALSE;
    }
   
    /**
     * Rewinds the directory to the beginning.
     *
     * @throws  io.IOException in case an error occurs
     */
    public function rewind() {
      if (FALSE === $this->_hdir)
        throw new IOException ('Cannot rewind non-open folder.');
      
      rewinddir ($this->_hdir->handle);
    }

    /**
     * Retrieve when the folder was created
     *
     * @return  int The date the file was created as a unix-timestamp
     * @throws  io.IOException in case of an error
     */
    public function createdAt() {
      if (FALSE === ($mtime= filectime($this->uri))) {
        throw new IOException('Cannot get mtime for '.$this->uri);
      }
      return $mtime;
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
        throw new IOException('Cannot get atime for '.$this->uri);
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
        throw new IOException('Cannot get mtime for '.$this->uri);
      }
      return $mtime;
    }

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(uri= %s)',
        $this->getClassName(),
        $this->uri
      );
    }

    /**
     * Return if the folder was already opened
     *
     * @return  bool
     */
    public function isOpen() {
      return is_resource($this->_hdir);
    }
  }
?>
