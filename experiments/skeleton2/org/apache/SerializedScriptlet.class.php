<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for serialization of scriptlets.
   *
   * A serialized scriptlet only processes one request
   * at a time in the semaphore scope. Depending
   * on the scope that can be one request globally or
   * per session, per host, etc...
   *
   * @see      xp://util.semaphore.Semaphore
   * @purpose  Serialize requests of scriptlets
   */
  interface SerializedScriptlet {

    /**
     * Lock the request
     *
     * @access  public
     * @return  bool success
     */
    public function lock();
    
    /**
     * Unlock the request
     *
     * @access  public
     * @return  bool success
     */
    public function unlock();    
  }
?>
