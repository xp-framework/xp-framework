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
    public
      $base   = array(),
      $root   = '';

    /**
     * Constructor
     *
     * @return  string root
     */
    public function __construct($root) {
      $this->root= realpath(rtrim($root, DIRECTORY_SEPARATOR));
    }
    
    /**
     * Helper method
     *
     * @param   string clientId
     * @param   string uri
     * @return  string
     */
    public function realname($clientId, $uri) {

      // Short-circuit this
      if (NULL === $uri) {
        return $this->root.DIRECTORY_SEPARATOR.$this->base[$clientId];
      }

      $uri= strtr($uri, '/', DIRECTORY_SEPARATOR);    // External uris use "/"
      $path= (DIRECTORY_SEPARATOR === $uri{0} && isset($this->base[$clientId])
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
     * @param   int clientId
     * @param   string uri
     * @return  string new base
     */
    public function setBase($clientId, $uri= NULL) {
      if (!is_dir($path= $this->realname($clientId, $uri))) {
        throw new IOException($uri.': not a directory');
      }
      $this->base[$clientId]= DIRECTORY_SEPARATOR.ltrim(
        str_replace($this->root, '', $path),
        DIRECTORY_SEPARATOR
      );
      
      // Return base directory for a given client - all
      // directories returned should use forward slashes!
      return strtr($this->base[$clientId], DIRECTORY_SEPARATOR, '/');
    }
    
    /**
     * Retrieves base
     *
     * @param   int clientId
     * @return  string
     */
    public function getBase($clientId) {
      if (!isset($this->base[$clientId])) { $this->setBase($clientId); }
      
      // Return base directory for a given client - all
      // directories returned should use forward slashes!
      return strtr($this->base[$clientId], DIRECTORY_SEPARATOR, '/');
    }
    
    /**
     * Creates a new StorageElement or StorageCollection (depending on
     * type)
     *
     * @param   string clientId
     * @param   string uri
     * @param   int type
     * @return  peer.ftp.server.storage.StorageEntry
     */
    public function createEntry($clientId, $uri, $type) {
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
     * @param   string uri
     * @param   int type one of the ST_* constants
     * @return  peer.ftp.server.storage.StorageEntry
     */
    public function create($clientId, $uri, $type) {
      $path= $this->realname($clientId, $uri);

      switch ($type) {
        case ST_ELEMENT:
          if (FALSE === touch($path)) {
            throw new IOException('File '.$path.' could not be created');
          }
          break;
        
        case ST_COLLECTION:
          if (FALSE === mkdir($path)) {
            throw new IOException($path.' could not be created');
          }
          break;
      }
      return $this->createEntry($clientId, $uri, $type);
    }

    /**
     * Looks up a element. Returns a StorageCollection, a StorageElement 
     * or NULL in case it nothing is found.
     *
     * @param   string uri
     * @return  peer.ftp.server.storage.StorageEntry
     */
    public function lookup($clientId, $uri) {
      if (!file_exists($path= $this->realname($clientId, $uri))) return NULL;

      return $this->createEntry($clientId, $uri, is_dir($path) ? ST_COLLECTION : ST_ELEMENT);
    }
  }
?>
