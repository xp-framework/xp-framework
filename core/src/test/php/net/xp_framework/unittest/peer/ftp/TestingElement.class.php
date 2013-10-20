<?php namespace net\xp_framework\unittest\peer\ftp;

use peer\ftp\server\storage\StorageElement;


/**
 * Memory storage used
 *
 * @see   xp://net.xp_framework.unittest.peer.ftp.TestingServer
 */
class TestingElement extends \lang\Object implements StorageElement {
  protected $name= '';
  protected $storage= null;
  protected $perm= 0666;
  protected $contents= '';
  protected $offset= 0;

  /**
   * Constructor
   *
   * @param  string name
   * @param  peer.ftp.server.storage.Storage storage
   * @param  string contents
   */
  public function __construct($name, $storage, $contents= '') {
    $this->name= $name;
    $this->storage= $storage;
    $this->contents= $contents;
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
    return strlen($this->contents);
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
   * Open this element with a specified mode
   *
   * @param   string mode of of the SE_* constants
   */
  public function open($mode) {
    $this->offset= 0;
  }
  
  /**
   * Read a chunk of data from this element
   *
   * @return  string
   */
  public function read() {
    if ($this->offset >= strlen($this->contents)) return false;
    $chunk= substr($this->contents, $this->offset, 4096);
    $this->offset+= strlen($chunk);
    return $chunk;
  }
  
  /**
   * Write a chunk of data to this element
   *
   * @param   string buf
   */
  public function write($buf) {
    $this->contents.= $buf;
  }
  
  /**
   * Close this element
   *
   */
  public function close() {
    $this->offset= 0;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return $this->getClassName().'('.$this->name.', '.strlen($this->content).' bytes)';
  }
}
