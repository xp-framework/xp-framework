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
    var
      $name=    '',
      $storage= NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string name default 'semaphore'
     */  
    function __construct(&$storage, $name= 'semaphore') {
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
    function lock() { }
    
    /**
     * Remove the semaphore to unlock
     *
     * @model   abstract
     * @access  public
     * @return  bool succeed
     */
    function unlock() { }
    
    /**
     * Retrieve the creation time of the semaphore
     * as UNIX-timestamp
     *
     * @model   abstract
     * @access  public
     * @return  int utime
     */
    function getCreatedAt() { }
    
  }
?>
