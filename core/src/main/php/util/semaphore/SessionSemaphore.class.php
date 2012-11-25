<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.semaphore.Semaphore',
    'util.profiling.Timer'
  );

  /**
   * SessionSemaphore class. This class can be used to
   * serialize multiple requests on e.g. a webserver cluster.
   * 
   * Attention: while this class generally takes an
   * scriptlet.HttpSession object, it somewhat depends on the internal
   * behaviour of a session: as it is absolutely necessary for the
   * semaphore to be stored in a central storage at the first 
   * opportunity, some session systems (as the PHP builtin one)
   * "cache" their contents and flush them out on script end.
   * This circumvents any use of the semaphore.
   * 
   * Also, when using clustered systems, the storage for the session
   * data must be global to all parts of the cluster, that means
   * they all must be using the same session storage. This may or
   * may not be the case with yours. 
   *
   * @ext      session
   * @purpose  Wrap session semaphores
   * @deprecated
   */
  class SessionSemaphore extends Semaphore {

    /**
     * Constructor
     *
     * @param   scriptlet.HttpSession storage
     * @param   string name default 'semaphore'
     */
    public function __construct(HttpSession $storage, $name= 'semaphore') {
      parent::__construct($storage, 'xp-'.$name);
    }
  
    /**
     * Set the semaphore to lock.
     *
     * @return  bool succeed
     */
    public function lock() {
      if ($this->storage->hasValue($this->name)) return FALSE;
        
      $this->storage->putValue($this->name, time());
      return TRUE;
    }
    
    /**
     * Remove the semaphore to unlock.
     *
     * @return  bool succeed
     */
    public function unlock() {
      $this->storage->removeValue($this->name);
      return TRUE;
    }
    
    /**
     * Retrieve the creation time of the semaphore
     * as UNIX-timestamp
     *
     * @return  int utime
     */
    public function getCreatedAt() {
      return $this->storage->getValue($this->name);
    }
  }
?>
