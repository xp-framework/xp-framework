<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Semaphore
   *
   * <code>
   *   $s= &Semaphore::get(6100);
   *   $s->acquire();
   *   // [...]
   *   $s->release();
   *   $s->remove();
   * </code>
   *
   * @purpose   Provide a wrapper around semaphores
   * @ext       sem
   */
  class Semaphore extends Object {
    var
      $key       = 0,
      $maxAquire = 1;
      
    var
      $_hdl      = NULL;
      
    /**
     * Get a semaphore
     *
     * @model   static
     * @access  public
     * @param   int key
     * @param   int maxAquire default 1
     * @return  &io.sys.Semaphore a semaphore
     * @throws  IOException
     */
    function &get($key, $maxAquire= 1) {
      $s= &new Semaphore();
      $s->key= $key;
      $s->maxAquire= $maxAquire;
      if (FALSE === ($s->_hdl= sem_get($key, $maxAquire))) {
        return throw(new IOException('Could not get semaphore '.$key));
      }
      
      return $s;
    }
    
    /**
     * Acquire a semaphore
     *
     * @access  public
     * @return  bool success
     * @throws  IOException
     */
    function acquire() {
      if (FALSE === sem_acquire($this->_hdl)) {
        return throw(new IOException('Could not acquire semaphore '.$this->key));
      }
      return TRUE;
    }
    
    /**
     * Release a semaphore
     *
     * @access  public
     * @return  bool success
     * @throws  IOException
     */
    function release() {
      if (FALSE === sem_release($this->_hdl)) {
        return throw(new IOException('Could not release semaphore '.$this->key));
      }
      return TRUE;
    }
    
    /**
     * Remove a semaphore
     *
     * @access  public
     * @return  bool success
     * @throws  IOException
     */
    function remove() {
      if (FALSE === sem_remove($this->_hdl)) {
        return throw(new IOException('Could not release semaphore '.$this->key));
      }
      return TRUE;
    }
  }
?>
