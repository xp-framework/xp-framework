<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.semaphore.Semaphore',
    'org.apache.HttpSession',
    'util.profiling.Timer'
  );

  /**
   * SessionSemaphore class. This class can be used to
   * serialize multiple requests on e.g. a webserver cluster.
   * 
   * Attention: while this class generally takes an
   * org.apache.HttpSession object, it somewhat depends on the internal
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
   */
  class SessionSemaphore extends Semaphore {

    /**
     * Constructor
     *
     * @access  public
     * @param   &org.apache.HttpSession
     */
    public function __construct($storage, $name= 'semaphore') {
      if (!is('org.apache.HttpSession', $storage))
        throw (new IllegalArgumentException('Given argument is not a HttpSession'));
      
      parent::__construct($storage, 'xp-'.$name);
    }
  
    /**
     * Set the semaphore to lock.
     *
     * @model   abstract
     * @access  public
     * @return  bool succeed
     */
    public abstract function lock();
    
    /**
     * Remove the semaphore to unlock.
     *
     * @model   abstract
     * @access  public
     * @return  bool succeed
     */
    public abstract function unlock();
    
    /**
     * Retrieve the creation time of the semaphore
     * as UNIX-timestamp
     *
     * @model   abstract
     * @access  public
     * @return  int utime
     */
    public abstract function getCreatedAt();
  }
?>
