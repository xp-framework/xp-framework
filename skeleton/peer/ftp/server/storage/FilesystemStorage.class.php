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
      $base = array(),
      $root = '';

    /**
     * Constructor
     *
     * @access  public
     * @return  string root
     */
    function __construct($root) {
      $this->root= rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string uri
     * @return  string
     */
    function realname($clientId, $uri) {
      return (DIRECTORY_SEPARATOR == $uri{0}
        ? $this->root
        : rtrim($this->root.$this->base[$clientId], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR
      ).preg_replace('#^[/\.]+#', '', $uri);
    }

    /**
     * Sets base
     *
     * @access  public
     * @param   int clientId
     * @param   string uri
     * @return  string new base
     */
    function setBase($clientId, $uri) {
      if (!is_dir($path= $this->realname($clientId, $uri))) {
        return throw(new IOException($uri.': not a directory'));
      }
      $this->base[$clientId]= str_replace($this->root, '', $path);
      return $this->base[$clientId];
    }
    
    /**
     * Retrieves base
     *
     * @access  public
     * @param   int clientId
     * @return  string
     */
    function getBase($clientId) {
      return $this->base[$clientId];
    }
    
    /**
     * Creates a new StorageEntry and return it
     *
     * @access  public
     * @param   string uri
     * @param   int type one of the ST_* constants
     * @return  &peer.ftp.server.storage.StorageEntry
     */
    function &create($clientId, $uri, $type) {
      $path= $this->realname($clientId, $uri);

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
    function &lookup($clientId, $uri) {
      if (!file_exists($path= $this->realname($clientId, $uri))) return NULL;
      
      if (is_dir($path)) {
        return new FilesystemStorageCollection($path);
      } else {
        return new FilesystemStorageElement($path);
      }
    }
  }
?>
