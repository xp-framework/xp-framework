<?php namespace xp\codegen;

/**
 * Storage address
 */
class StorageAddress extends \lang\Object {
  protected 
    $storage= null,
    $name   = '';    
  
  /**
   * Constructor
   *
   * @param   xp.codegen.AbstractStorage storage
   * @param   string name
   */
  public function __construct(AbstractStorage $storage, $name) {
    $this->storage= $storage;
    $this->name= $name;
  }

  /**
   * Get storage name
   *
   * @return  string
   */
  public function name() {
    return $this->name;
  }
  
  /**
   * Get stored data
   *
   * @return  string
   */
  public function data() {
    return $this->storage->read($this->name);
  }
}
