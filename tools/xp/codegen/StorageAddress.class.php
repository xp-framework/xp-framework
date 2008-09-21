<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Storage address
   *
   * @purpose  purpose
   */
  class StorageAddress extends Object {
    protected 
      $storage= NULL,
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
?>
