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
      $base   = array(),
      $root   = '';

    /**
     * Constructor
     *
     * @access  public
     * @return  string root
     */
    function __construct($root) {
      $this->root= rtrim($root, DIRECTORY_SEPARATOR);
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string uri
     * @return  string
     */
    function realname($clientId, $uri) {
      $path= (DIRECTORY_SEPARATOR == $uri{0}
        ? $uri
        : $this->base[$clientId].DIRECTORY_SEPARATOR.$uri
      );
      
      with (
        $parts= explode(DIRECTORY_SEPARATOR, $path),
        $stack= array()
      ); {
        foreach ($parts as $part) {
          switch ($part) {
            case '.':
            case '':
              break;

            case '..':
              array_pop($stack);
              break;

            default:
              $stack[]= $part;
          }
        }
      }
      
      return $this->root.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $stack);
    }

    /**
     * Sets base
     *
     * @access  public
     * @param   int clientId
     * @param   string uri
     * @return  string new base
     */
    function setBase($clientId, $uri= NULL) {
      if (!is_dir($path= $this->realname($clientId, $uri))) {
        return throw(new IOException($uri.': not a directory'));
      }
      $this->base[$clientId]= DIRECTORY_SEPARATOR.ltrim(
        str_replace($this->root, '', $path),
        DIRECTORY_SEPARATOR
      );
      
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
      if (NULL == $this->base[$clientId]) { $this->setBase($clientId); }
      return $this->base[$clientId];
    }
    
    /**
     * Creates a new StorageElement or StorageCollection (depending on
     * type)
     *
     * @access public
     * @param string clientId
     * @param string uri
     * @param int type
     * @return &peer.ftp.server.storage.StorageEntry
     */
    function &createEntry($clientId, $uri, $type) {
      $path= substr($this->realname($clientId, $uri), strlen($this->root));
      switch ($type) {
        case ST_ELEMENT:
          return new FilesystemStorageElement($path, $this->root);
          
        case ST_COLLECTION:
          return new FilesystemStorageCollection($path, $this->root);
      }
      return xp::null();
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
          break;
        
        case ST_COLLECTION:
          if (FALSE === mkdir($path)) {
            return throw(new IOException($path.' could not be created'));
          }
          break;
      }
      return $this->createEntry($clientId, $uri, $type);
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
      
      return $this->createEntry($clientId, $uri, is_dir($path) ? ST_COLLECTION : ST_ELEMENT);
    }
  }
?>
