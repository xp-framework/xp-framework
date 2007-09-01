<?php
/* This class is part of the XP framework
 *
 * $Id: SingleProcess.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace util::cmd;
 
  ::uses('io.File');

  /**
   * SingleProcess provides a way to insure a process is only running
   * once at a time.
   * 
   * Usage:
   * <code>
   *   $sp= &new SingleProcess();
   *   if (!$sp->lock()) {
   *     exit(-1);
   *   }
   *
   *   // [...operation which should only take part once at a time...]
   *
   *   $sp->unlock();
   * </code>
   *
   * @purpose  Lock process so it can only be run once
   */  
  class SingleProcess extends lang::Object {
    public 
      $lockfile     = NULL;

    /**
     * Constructor
     *
     * @param   string lockfileName default NULL the lockfile's name,
     *          defaulting to <<program_name>>.lck
     */
    public function __construct($lockFileName= NULL) {
      if (NULL === $lockFileName) $lockFileName= $_SERVER['argv'][0].'.lck';
      $this->lockfile= new io::File($lockFileName);
    }
    
    /**
     * Lock this application
     *
     * @return  bool success
     */
    public function lock() {
      try {
        $this->lockfile->open(FILE_MODE_WRITE);
        $this->lockfile->lockExclusive();
      } catch (io::IOException $e) {
        $this->lockfile->close();
        return FALSE;
      }
      
      return TRUE;
    }
    
    /**
     * Unlock the application
     *
     * @return  bool Success
     */
    public function unlock() {
      if ($this->lockfile->unlock()) {
        $this->lockfile->close();
        $this->lockfile->unlink();
        return TRUE;
      }
      
      return FALSE;
    }
  }
?>
