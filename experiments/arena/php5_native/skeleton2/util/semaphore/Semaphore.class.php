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
     * @access  public
     * @param   string name default 'semaphore'
     */  
    public function __construct(&$storage, $name= 'semaphore') {
      $this->storage= &$storage;
      $this->name= $name;
    }
    
    /**
     * Set the semaphore to lock
     *
     * @model   abstract
     * @access  public
     * @return  bool succeed
     */
    public function lock() { }
    
    /**
     * Remove the semaphore to unlock
     *
     * @model   abstract
     * @access  public
     * @return  bool succeed
     */
    public function unlock() { }
    
    /**
     * Retrieve the creation time of the semaphore
     * as UNIX-timestamp
     *
     * @model   abstract
     * @access  public
     * @return  int utime
     */
    public function getCreatedAt() { }
    
  }
?>
