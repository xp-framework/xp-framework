<?php
  /* SingleProcess.class.php
     Es darf immer nur ein Prozess laufen. Macht natürlich nur kommandozeilenbasiert Sinn
     
     $Id$
  */

  define('PID_FILE_PATH',	'/var/run/php/');
  
  class SingleProcess extends Object {
    var $pid, $pname, $lockfile, $Debug, $Error;
    
    function SingleProcess($params= NULL) {
      $this->__construct();
    }
     
    function __construct($params= NULL) {
      parent::__construct(); 
      $this->pname= basename($GLOBALS["argv"][0]);
      $this->Debug= $this->Error= 0;
      $this->pid= getmypid();
      $this->lockfile= sprintf('%s/%s.lck', PID_FILE_PATH, $this->pname);
    }
    
    function logline_text($key, $val) {
      if($this->Debug) logline_text("SingleProcess::$key", $val);
    }

    function raise_error($e_code, $comment) {
      $this->logline_text("SingleProcess:raise_error", "{errno} $e_code {comment} $comment");
      $this->Error= $e_code;
      return 0;
    }

    function _lock() {
      $this->logline_text("locking", $this->lockfile);
      $fd= fopen($this->lockfile, "w");
      if(!$fd) return $this->raise_error(4, "cannot write to $this->lockfile");
      fputs($fd, $this->pid);
      fclose($fd);
      return 1;
    }
    
    function lock() {
      if($this->is_running()) return $this->raise_error(1, "already running");
      return $this->_lock();
    }
    
    function unlock() {
      $this->logline_text("unlocking", $this->lockfile);
      return unlink($this->lockfile);
    }
    
    function is_running() {
      if(file_exists($this->lockfile)) {
        list($pid)= file($this->lockfile);
        
        if (file_exists('/proc/'.$pid)) {
          $this->logline_text("is_running", "this process is running under pid $pid");
          return 1;
        }
        
        return $this->raise_error(4, "stale lockfile, pid $pid doesn't exist");
      }
      return 0;
    }
  } // end::class(SingleProcess)
?>
