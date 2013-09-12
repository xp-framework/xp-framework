<?php namespace net\xp_framework\unittest\peer\ftp;

use peer\ftp\server\storage\Storage;


/**
 * Memory storage used
 *
 * @see   xp://net.xp_framework.unittest.peer.ftp.TestingServer
 */
class TestingStorage extends \lang\Object implements Storage {
  protected $base= array();
  public $entries= array();

  /**
   * Sets base
   *
   * @param  int clientId
   * @param  string uri
   */
  public function setBase($clientId, $uri) {
    $this->base[$clientId]= $this->normalize($this->base[$clientId], $uri);
    return $this->base[$clientId];
  }

  /**
   * Adds a storage entry
   *
   * @param   peer.ftp.server.storage.StorageEntry $e
   */
  public function add(\peer\ftp\server\storage\StorageEntry $e) {
    $this->entries[$e->getFileName()]= $e;
  }

  /**
   * Gets base
   *
   * @param  int clientId
   * @return string uri
   */
  public function getBase($clientId) {
    if (!isset($this->base[$clientId])) {
      $this->base[$clientId]= '/';
    }
    return $this->base[$clientId];
  }

  /**
   * Normalize a given URI
   * 
   * @param   string base
   * @param   string uri
   * @return  string
   */
  protected function normalize($base, $uri) {
    if ('/' !== $uri{0}) $uri= $base.'/'.$uri;
    $r= '';
    $o= 0;
    $l= strlen($uri);
    do {
      $p= strcspn($uri, '/', $o);
      $element= substr($uri, $o, $p);
      if ('' === $element || '.' === $element) {
        // NOOP
      } else if ('..' === $element) {
        $r= substr($r, 0, strrpos($r, '/', -2)).'/';
      } else {
        $r.= $element.'/';
      }
      $o+= $p+ 1;
    } while ($o < $l);
    return '/'.rtrim($r, '/');
  }

  /**
   * Helper method
   *
   * @param   string clientId
   * @param   string uri
   * @return  string
   */
  public function realname($clientId, $uri) {
    return $this->normalize($this->base[$clientId], $uri);
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
    $qualified= $this->normalize($this->base[$clientId], $uri);
    switch ($type) {
      case ST_ELEMENT:
        return new TestingElement($qualified, $this);
        break;

      case ST_COLLECTION:
        return new TestingCollection($qualified, $this);
        break;
    }
    return \xp::null();
  }

  /**
   * Looks up an entry
   *
   * @param  int clientId
   * @param  string uri
   * @return peer.ftp.server.storage.StorageEntry 
   */
  public function lookup($clientId, $uri) {
    $qualified= $this->normalize($this->base[$clientId], $uri);
    // Logger::getInstance()->getCategory()->warn('*** LOOKUP', $qualified, $this->entries[$qualified]);
    return isset($this->entries[$qualified]) ? $this->entries[$qualified] : null;
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
    $qualified= $this->normalize($this->base[$clientId], $uri);
    $this->entries[$qualified]= $this->createEntry($clientId, $uri, $type);
    return $this->entries[$qualified];
  }
}
