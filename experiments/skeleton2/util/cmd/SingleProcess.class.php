<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.File');

  /**
   * SingleProcess provides a way to insure a process is only running
   * once at a time.
   * 
   * Usage:
   * <code>
   *   $sp= new SingleProcess();
   *   try(); {
   *     $sp->lock();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *
   *   // [...operation which should only take part once at a time...]
   *
   *   try(); {
   *     $sp->unlock();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   * </code>
   *
   * @purpose  Lock process so it can only be run once
   */  
  class SingleProcess extends Object {
    public 
      $pid          = 0, 
      $lockfile     = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string lockfileName default NULL the lockfile's name,
     *          defaulting to <<program_name>>.lck
     */
    public function __construct($lockFileName= NULL) {
      if (NULL === $lockFileName) $lockFileName= $_SERVER['argv'][0].'.lck';
      $this->pid= getmypid();
      $this->lockfile= new File($lockFileName);
       
    }
    
    /**
     * Return a checksum
     *
     * @access  private
     * @return  string
     */
    private function _checksum($pid) {
      return md5_file('/proc/'.$pid.'/cmdline');
    }
    
    /**
     * Lock this application
     *
     * @access  public
     * @return  bool success
     * @throws  IllegalStateException in case this application is already running
     */
    public function lock() {
      if (FALSE !== ($pid= self::isRunning())) {
        throw (new IllegalStateException('already running under pid '.$pid)); 
      }
      try {
        $this->lockfile->open(FILE_MODE_WRITE);
        $this->lockfile->write(pack(
          'i1a32', 
          $this->pid, 
          self::_checksum($this->pid)
        ));
        $this->lockfile->close();
      } catch (IOException $e) {
        throw ($e);
      }
      return 1;
    }
    
    /**
     * Unlock the application
     *
     * @access  public
     * @return  bool Success
     */
    public function unlock() {
      return $this->lockfile->unlink();
    }
    
    /**
     * Returns whether the application is still running.
     * 
     * Warning: This will only work on systems that have the /proc filesystem.
     * For others: TBD - figure out where to get this information from!
     *
     * @access  public
     * @return  int pid process' id the locked application is running under or
     *          FALSE to indicate the process is no longer running
     */
    public function isRunning() {
      if (!$this->lockfile->exists()) return FALSE;
      
      // Read our lockfile
      try {
        $this->lockfile->open(FILE_MODE_READ);
        $data= unpack('i1pid/a32checksum', $this->lockfile->read(36));
        $this->lockfile->close();
      } catch (IOException $e) {
        throw ($e);
      }
      
      // Is there a process with this pid and does the checksum match us?
      if (
        (is_dir('/proc/'.$data['pid'])) &&
        ($data['checksum'] == self::_checksum($data['pid']))
      ) {
        return $data['pid'];
      }
      
      // Stale lockfile, remove it
      self::unlock();
      return FALSE;
    }
  }
?>
