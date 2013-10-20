<?php namespace net\xp_framework\unittest\peer\ftp;

use peer\ftp\server\storage\StorageCollection;


/**
 * Memory storage used
 *
 * @see   xp://net.xp_framework.unittest.peer.ftp.TestingServer
 */
class TestingCollection extends \lang\Object implements StorageCollection {
  protected $name= '';
  protected $storage= null;
  protected $perm= 040777;        // drwxrwxrx

  /**
   * Constructor
   *
   * @param  string name
   * @param  peer.ftp.server.storage.Storage storage
   */
  public function __construct($name, $storage) {
    $this->name= $name;
    $this->storage= $storage;
  }

  /**
   * Deletes an entry
   *
   * @return  bool TRUE to indicate success
   */
  public function delete() {
    unset($this->storage->entries[$this->name]);
    return true;
  }

  /**
   * Renames an entry
   *
   * @param   string target
   * @return  bool TRUE to indicate success
   */
  public function rename($target) {
    unset($this->storage->entries[$this->name]);
    $this->name= $target;
    $this->storage->entries[$target]= $this;
    return true;
  }

  /**
   * Returns the filename including the path (relative to storage root)
   *
   * @return string
   */
  public function getFilename() {
    return $this->name;
  }

  /**
   * Retrieves the (short) name of a storage entry
   *
   * @return  string
   */  
  public function getName() {
    return basename($this->name);
  }
  
  /**
   * Retrieves the owner's username
   *
   * @return  string
   */  
  public function getOwner() {
    return 'test';
  }

  /**
   * Retrieves the owner's group name
   *
   * @return  string
   */  
  public function getGroup() {
    return 'testers';
  }
  
  /**
   * Retrieves the size of this storage entry
   *
   * @return  int bytes
   */  
  public function getSize() {
    return 0;
  }

  /**
   * Retrieves the modified timestamp of this storage entry
   *
   * @return  int unix timestamp
   */  
  public function getModifiedStamp() {
    return time();
  }
  
  /**
   * Retrieves the permissions of this storage entry expressed in a
   * unix-permission style integer
   *
   * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
   * @return  int
   */  
  public function getPermissions() {
    return $this->perm;
  }

  /**
   * Sets the permissions of this storage entry expressed in a
   * unix-permission style integer
   *
   * @param   int permissions
   */  
  public function setPermissions($permissions) {
    $this->perm= $permissions;
  }

  /**
   * Retrieves the number of links
   *
   * @return  int
   */  
  public function numLinks() {
    return 1;
  }

  /**
   * Retrieves a list of elements
   *
   * @return  peer.ftp.server.storage.StorageEntry[]
   */
  public function elements() {
    $cmp= rtrim($this->name, '/').'/';
    $r= array(new self($cmp.'.', $this->storage), new self($cmp.'..', $this->storage));
    foreach ($this->storage->entries as $name => $entry) {
      if ($cmp === substr($name, 0, strrpos($name, '/')+ 1) && $entry !== $this) $r[]= $entry;
    }
    // Logger::getInstance()->getCategory()->warn('*** LS', $this, $r);
    return $r;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this->name.')';
  }
}
