<?php
/* This class is part of the XP framebase
 *
 * $Id$ 
 */

  uses(
    'peer.ftp.server.storage.FilesystemStorageCollection',
    'peer.ftp.server.storage.FilesystemStorageElement'
  );

  /**
   * This interface describes objects that implement a storage for FTP
   * servers.
   *
   * @purpose  Storage
   */
  class FilesystemStorage extends Object {
    var
      $base = DIRECTORY_SEPARATOR,
      $root = '';

    /**
     * Constructor
     *
     * @access  public
     * @return  string root
     */
    function __construct($root) {
      $this->root= rtrim($root, DIRECTORY_SEPARATOR).$this->base;
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string uri
     * @return  string
     */
    function realname($uri) {
      return realpath($this->root.$this->base.preg_replace('#^[/\.]+#', '', $uri));
    }

    /**
     * Sets base
     *
     * @access  public
     * @param   string uri
     * @return  string new base
     */
    function setBase($uri) {
      if (!is_dir($path= $this->realname($uri))) {
        return throw(new IOException($path.' is not a directory'));
      }
      $this->base= str_replace($this->root, '', $path);
      return $this->base;
    }
    
    /**
     * Retrieves base
     *
     * @access  public
     * @return  string
     */
    function getBase() {
      return $this->base;
    }
    
    /**
     * Creates a new StorageEntry and return it
     *
     * @access  public
     * @param   string uri
     * @param   int type one of the ST_* constants
     * @return  &peer.ftp.server.storage.StorageEntry
     */
    function &create($uri, $type) {
      $path= $this->realname($uri);

      switch ($type) {
        case ST_ELEMENT:
          if (FALSE === touch($path)) {
            return throw(new IOException('File '.$path.' could not be created'));
          }
          return new FilesystemStorageElement($path);
        
        case ST_COLLECTION:
          if (FALSE === mkdir($path)) {
            return throw(new IOException($path.' could not be created'));
          }
          return new FilesystemStorageCollection($path);
      }
      return xp::null();
    }

    /**
     * Looks up a element. Returns a StorageCollection, a StorageElement 
     * or NULL in case it nothing is found.
     *
     * @access  public
     * @param   string uri
     * @return  &peer.ftp.server.storage.StorageEntry
     */
    function &lookup($uri) {
      if (!file_exists($path= $this->realname($uri))) return NULL;
      
      if (is_dir($path)) {
        return new FilesystemStorageCollection($path);
      } else {
        return new FilesystemStorageElement($path);
      }
    }
  }
?>
