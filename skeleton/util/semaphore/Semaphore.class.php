<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Semaphore class
   *
   * @see      http://
   * @purpose  Semaphore to serialize request
   */
  class Semaphore extends Object {
    public
      $name=    '',
      $storage= NULL;
  
    /**
     * Constructor
     *
     * @param   string name default 'semaphore'
     */  
    public function __construct($storage, $name= 'semaphore') {
      $this->storage= $storage;
      $this->name= $name;
    }
    
    /**
     * Set the semaphore to lock
     *
     * @return  bool succeed
     */
    public function lock() { }
    
    /**
     * Remove the semaphore to unlock
     *
     * @return  bool succeed
     */
    public function unlock() { }
    
    /**
     * Retrieve the creation time of the semaphore
     * as UNIX-timestamp
     *
     * @return  int utime
     */
    public function getCreatedAt() { }
    
  }
?>
