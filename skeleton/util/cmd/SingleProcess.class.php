<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
 
  uses('io.File');

  /**
   * Eine SingleTon für Prozesse
   *
   */  
  class SingleProcess extends Object {
    var 
      $pid, 
      $lockfile;

    /**
     * Constructor
     *
     * @param   string lockfileName default NULL Das Lock-File (defaultet auf <<PROGRAMM_NAME>>.lck)
     */
    function __construct($lockFileName= NULL) {
      parent::__construct(); 
      if (NULL == $lockFileName) $lockFileName= $_SERVER['argv'][0].'.lck';
      $this->pid= getmypid();
      $this->lockfile= new File($lockFileName);
    }
    
    /**
     * Lockt den Prozess
     *
     * @access  public
     * @return  bool Success
     * @throws  IllegalStateException, wenn versucht wird, den Prozess zu locken,
     *          obwohl er bereist läuft
     */
    function lock() {
      if ($this->lockfile->exists()) {
        return throw(new IllegalStateException('already running')); 
      }
      try(); {
        $this->lockfile->open(FILE_MODE_WRITE);
        $this->lockfile->write($this->pid);
        $this->lockfile->close();
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      return 1;
    }
    
    /**
     * Unlockt den Prozess
     *
     * @access  public
     * @return  bool Success
     */
    function unlock() {
      return $this->lockfile->unLink();
    }
    
    /**
     * Gibt zurück, ob der Prozess noch läuft. TODO: Funktioniert nur unter UNIXoiden Systemen,
     * da das /proc/-Filesystem benutzt wird! Unter Windows müsste man nochmal sehen,
     * wie das funktioniert
     *
     * @access  public
     * @return  int pid Prozess-ID des laufenden Prozesses oder FALSE
     */
    function isRunning() {
      if (!$this->lockfile->exists()) return FALSE;
      
      // Schauen wir nach der PID
      try(); {
        $this->lockfile->open(FILE_MODE_READ);
        $pid= $this->lockfile->readLine();
        $this->lockfile->close();
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      if (file_exists('/proc/'.$pid)) return $pid;
      
      // Wir haben ein "stale lockfile"...
      return FALSE;
    }
  }
?>
