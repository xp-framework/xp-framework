<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.Folder',
    'peer.ftp.server.storage.FilesystemStorageElement',
    'peer.ftp.server.storage.StorageCollection'
  );

  /**
   * Implements the StorageCollection via filesystem
   *
   * @ext      posix
   * @purpose  StorageCollection
   */
  class FilesystemStorageCollection extends Object implements StorageCollection {
    public
      $root = NULL,
      $path = NULL,
      $f    = NULL,
      $name = '';

    /**
     * Constructor
     *
     * @param string path The path to resource (including directory's name)
     * @param string root The FTP root directory
     * @return  string uri
     */
    public function __construct($path, $root) {
      $this->path= $path;
      $this->root= $root;
      $uri= $root.$path;
      if ('..' == substr($uri, -2)) {
        $this->name= '..';
      } else if ('.' == substr($uri, -1)) {
        $this->name= '.';
      } else {
        $this->name= basename($uri);
      }
      $this->f= new Folder($uri);
      $this->st= stat($this->f->getURI());
      if (!extension_loaded('posix')) {
        $this->st['pwuid']= $this->st['grgid']= array('name' => 'none');
      } else {
        $this->st['pwuid']= posix_getpwuid($this->st['uid']);
        $this->st['grgid']= posix_getgrgid($this->st['gid']);
      }
    }

    /**
     * Deletes an entry
     *
     * @return  bool TRUE to indicate success
     */
    public function delete() { 
      $r= $this->f->unlink();
      clearstatcache();
      return $r;
    }

    /**
     * Renames an entry
     *
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    public function rename($target) { 
      $path= (DIRECTORY_SEPARATOR == $target{0}
        ? substr($this->f->getURI(), 0, strpos($this->f->getURI(), dirname($target)))
        : dirname($this->f->getURI()).DIRECTORY_SEPARATOR
      ).$target;
    
      $r= $this->f->move($path);
      clearstatcache();
      return $r;
    }

    /**
     * Returns the filename including the path (relative to storage root)
     *
     * @return string
     */
    public function getFilename() {
      return $this->path;
    }

    
    /**
     * Retrieves the (short) name of a storage entry
     *
     * @return  string
     */  
    public function getName() { 
      return $this->name;
    }
    
    /**
     * Retrieves the owner's username
     *
     * @return  string
     */  
    public function getOwner() { 
      return $this->st['pwuid']['name'];
    }

    /**
     * Retrieves the owner's group name
     *
     * @return  string
     */  
    public function getGroup() {
      return $this->st['grgid']['name'];
    }
    
    /**
     * Retrieves the size of this storage entry
     *
     * @return  int bytes
     */  
    public function getSize() { 
      return $this->st['size'];
    }

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @return  int unix timestamp
     */  
    public function getModifiedStamp() { 
      return $this->st['mtime'];
    }
    
    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @return  int
     */  
    public function getPermissions() { 
      return $this->st['mode'];
    }

    /**
     * Sets the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @param   int permissions
     */  
    public function setPermissions($permissions) {
      chmod($this->f->getURI(), intval((string)$permissions, 8));
      clearstatcache();
      $this->st['mode']= $permissions;
    }

    /**
     * Retrieves the number of links
     *
     * @return  string
     */
    public function numLinks() {
      return $this->st['nlink'];
    }
    
    /**
     * Retrieves a list of elements
     *
     * @return  peer.ftp.server.storage.StorageEntry[]
     */
    public function elements() {
      $rpath= substr($this->f->getURI(), strlen($this->root));
          
      $r= array();
      $r[]= new FilesystemStorageCollection($rpath.'.', $this->root);
      $r[]= new FilesystemStorageCollection($rpath.'..', $this->root);
      while ($entry= $this->f->getEntry()) {
        $path= $rpath.$entry;
        if (is_dir($this->root.$path)) {
          $r[]= new FilesystemStorageCollection($path, $this->root);
        } else {
          $r[]= new FilesystemStorageElement($path, $this->root);
        }
      }
      $this->f->rewind();
      return $r;
    }
  } 
?>
