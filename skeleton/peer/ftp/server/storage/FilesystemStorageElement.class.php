<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Implements the StorageElement via filesystem
   *
   * @ext      posix
   * @purpose  StorageElement
   */
  class FilesystemStorageElement extends Object {
    var
      $f    = NULL,
      $st   = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  string uri
     */
    function __construct($uri) { 
      $this->f= &new File($uri);
      $this->st= stat($this->f->getURI());
      $this->st['pwuid']= posix_getpwuid($this->st['uid']);
      $this->st['grgid']= posix_getgrgid($this->st['gid']);
    }

    /**
     * Deletes an entry
     *
     * @access  public
     * @return  bool TRUE to indicate success
     */
    function delete() { 
      return $this->f->unlink();
    }

    /**
     * Renames an entry
     *
     * @access  public
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    function rename($target) { 
      return $this->f->move($target);
    }

    /**
     * Retrieves the (short) name of a storage entry
     *
     * @access  public
     * @return  string
     */  
    function getName() { 
      return basename($this->f->getURI());
    }
    
    /**
     * Retrieves the owner's username
     *
     * @access  public
     * @return  string
     */  
    function getOwner() { 
      return $this->st['pwuid']['name'];
    }

    /**
     * Retrieves the owner's group name
     *
     * @access  public
     * @return  string
     */  
    function getGroup() {
      return $this->st['grgid']['name'];
    }
    
    /**
     * Retrieves the size of this storage entry
     *
     * @access  public
     * @return  int bytes
     */  
    function getSize() { 
      return $this->st['size'];
    }

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @access  public
     * @return  int unix timestamp
     */  
    function getModifiedStamp() { 
      return $this->st['mtime'];
    }
    
    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @access  public
     * @return  int
     */  
    function getPermissions() { 
      return $this->st['mode'];
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   int bits
     * @return  string
     */
    function _rwx($bits) {
      return (
        (($bits & 4) ? 'r' : '-').
        (($bits & 2) ? 'w' : '-').
        (($bits & 1) ? 'x' : '-')
      );
    }
    
    /**
     * Retrieve long string representation of this entry. This 
     * representation is used for the LIST command's output and should
     * look like the one you see when using ls -al:
     *
     * Example:
     * <pre>
     *   -rw-r--r--   1 thekid  thekid       738 Jun 24 14:21 stat.diff
     *   drwxr-xr-x   2 thekid  thekid       512 May 21 11:23 sync
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function longRepresentation() { 
      return sprintf(
        '-%s%s%s  %2d %s  %s  %8d %s %s',
        $this->_rwx(($this->st['mode'] >> 6) & 7),
        $this->_rwx(($this->st['mode'] >> 3) & 7),
        $this->_rwx(($this->st['mode']) & 7),
        $this->st['nlink'],
        $this->st['pwuid']['name'],
        $this->st['grgid']['name'],
        $this->st['size'],
        date('M d H:i', $this->st['mtime']),
        basename($this->f->getURI())
      );
    }

    /**
     * Open this element with a specified mode
     *
     * @access  public
     * @param   string mode of of the SE_* constants
     */
    function open($mode) { 
      switch ($mode) {
        case SE_READ: return $this->f->open(FILE_MODE_READ);
        case SE_WRITE: return $this->f->open(FILE_MODE_WRITE);
      }
    }
    
    /**
     * Read a chunk of data from this element
     *
     * @access  public
     * @return  string
     */
    function read() { 
      return $this->f->read();
    }
    
    /**
     * Write a chunk of data to this element
     *
     * @access  public
     * @param   string buf
     */
    function write($buf) { 
      return $this->f->write($buf);
    }
    
    /**
     * Close this element
     *
     * @access  public
     */
    function close() { 
      return $this->f->close();
    }
    
  } implements(__FILE__, 'peer.ftp.server.storage.StorageElement');
?>
