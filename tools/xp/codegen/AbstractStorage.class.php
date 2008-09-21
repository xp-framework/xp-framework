<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.codegen.StorageAddress');

  /**
   * Storage for generation
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractStorage extends Object {
    
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
?>
