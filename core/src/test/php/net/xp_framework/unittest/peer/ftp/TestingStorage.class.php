<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ftp.server.storage.Storage',
    'net.xp_framework.unittest.peer.ftp.TestingCollection',
    'net.xp_framework.unittest.peer.ftp.TestingElement'
  );

  /**
   * Memory storage used
   *
   * @see   xp://net.xp_framework.unittest.peer.ftp.TestingServer
   */
  class TestingStorage extends Object implements Storage {
    protected $base= array();
    public $entries= array();

    /**
     * Sets base
     *
     * @param  int clientId
     * @param  string uri
     */
    public function setBase($clientId, $uri) {
      $this->base[$clientId]= $uri;
    }

    /**
     * Gets base
     *
     * @param  int clientId
     * @return string uri
     */
    public function getBase($clientId) {
      return $this->base[$clientId];
    }

    /**
     * Gets an entry
     *
     * @param  int clientId
     * @param  string uri
     * @param  int type
     * @return peer.ftp.server.storage.StorageEntry
     */
    public function createEntry($clientId, $uri, $type) {
      $qualified= $this->base[$clientId].$uri;

      Console::writeLine('CreateEntry ', $qualified);
      return NULL;
    }

    /**
     * Looks up an entry
     *
     * @param  int clientId
     * @param  string uri
     * @return peer.ftp.server.storage.StorageEntry
     */
    public function lookup($clientId, $uri) {
      $qualified= $this->base[$clientId].$uri;
      return isset($this->entries[$qualified]) ? $this->entries[$qualified] : NULL;
    }

    /**
     * Creates an entry
     *
     * @param  int clientId
     * @param  string uri
     * @param  int type
     * @return peer.ftp.server.storage.StorageEntry
     */
    public function create($clientId, $uri, $type) {
      $qualified= $this->base[$clientId].$uri;

      switch ($type) {
        case ST_ELEMENT:
          $this->entries[$qualified]= new TestingElement($qualified, $this);
          break;

        case ST_COLLECTION:
          $this->entries[$qualified]= new TestingCollection($qualified, $this);
          break;
      }
      return $this->entries[$qualified];
    }
  }
?>
