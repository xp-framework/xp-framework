<?php namespace xp\codegen;

/**
 * Storage for generation
 */
abstract class AbstractStorage extends \lang\Object {
  
  /**
   * Get URI
   *
   * @return  string
   */
  public abstract function getUri();

  /**
   * Store data
   *
   * @param   string name
   * @param   string data
   */
  protected abstract function store($name, $data);

  /**
   * Fetch data
   *
   * @param   string name
   * @return  string data
   */
  protected abstract function fetch($name);
  
  /**
   * Write data
   *
   * @param   string name
   * @param   string data
   * @return  xp.codegen.StorageAddress
   */
  public function write($name, $data) {
    $this->store($name, $data);
    return new StorageAddress($this, $name);
  }

  /**
   * Read data
   *
   * @param   string name
   * @return  string data
   */
  public function read($name) {
    return $this->fetch($name);
  }
}
