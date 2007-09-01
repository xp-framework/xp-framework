<?php
/* This class is part of the XP framework
 *
 * $Id: Thread.class.php 9947 2007-04-10 09:30:46Z jens $ 
 */

  namespace lang;

  ::uses(
    'lang.IllegalThreadStateException',
    'lang.Runnable',
    'lang.SystemException'
  );

  /**
   * Thread
   *
   * <code>
   *   uses('lang.Thread');
   *   
   *   class TimerThread extends Thread {
   *     public
   *       $ticks    = 0,
   *       $timeout  = 0;
   *       
   *     public function __construct($timeout) {
   *       $this->timeout= $timeout;
   *       parent::__construct('timer.'.$timeout);
   *     }
   *       
   *     public function run() {
   *       while ($this->ticks < $this->timeout) {
   *         Thread::sleep(1000);
   *         $this->ticks++;
   *         printf("<%s> tick\n", $this->name);
   *       }
   *       printf("<%s> time's up!\n", $this->name);
   *     }
   *   }
   *   
   *   $t[0]= new TimerThread(5);
   *   $t[0]->start();
   *   $t[1]= new TimerThread(2);
   *   $t[1]->start();
   *   var_dump($t);
   *   for ($i= 0; $i < 3; $i++) {
   *     echo "<main> Waiting...\n";
   *     sleep(1);
   *   }
   *   var_dump($t[0]->join(), $t[1]->join());
   * </code>
   *
   * @ext      pcntl
   * @ext      posix
   * @see      xp://lang.Runnable
   * @purpose  Base class
   */
  class Thread extends Object {
    public
      $name     = '',
      $running  = FALSE;
      
    protected
      $target   = NULL,
      $_id      = -1,
      $_pid     = -1;
      
    /**
     * Constructor
     *
     * Implementation by subclassing:
     * <code>
     *   class ComputeThread extends Thread {
     *     public function run() {
     *       // ...
     *     }
     *   }
     *
     *   $thread= new ComputeThread('computr1');
     *   $thread->start();
     * </code>
     * 
     * Implementation by passing a Runnable: 
     * <code>
     *   $thread= new Thread(newinstance('lang.Runnable', array(), '{
     *     public function run() {
     *       // ...
     *     }
     *   }'));
     *   $thread->start();
     * </code>
     *
     * @param   mixed arg default NULL
     */
    public function __construct($arg= NULL) {
      if ($arg instanceof Runnable) {
        $this->target= $arg;
        $this->name= $arg->getClassName();
      } else {
        $this->target= $this;
        $this->name= $arg ? $arg : $this->getClassName();
      }
    }
    
    /**
     * Returns whether this thread is running
     *
     * @return  bool
     */
    public function isRunning() {
      return $this->running;
    }
    
    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Causes the currently executing thread to sleep (temporarily cease 
     * execution) for the specified number of milliseconds. 
     *
     * @param   int millis
     */
    public static function sleep($millis) {
      usleep($millis * 1000);
    }

    /**
     * Starts thread execution
     *
     * @throws  lang.IllegalThreadStateException if this thread is already running
     * @throws  lang.SystemException if the thread cannot be started
     */
    public function start() {
      if ($this->isRunning()) {
        throw(new IllegalThreadStateException('Already running'));
      }

      $parent= getmypid();
      $pid= pcntl_fork();
      if (-1 == $pid) {     // Cannot fork
        throw(new SystemException('Cannot fork'));
      } else if ($pid) {     // Parent
        $this->running= TRUE;
        $this->_id= $pid;
        $this->_pid= $parent;
      } else {              // Child
        $this->running= TRUE;
        $this->_id= getmypid();
        $this->_pid= $parent;
        call_user_func(array($this->target, 'run'));
        exit();
      }
    }
    
    /**
     * Join this thread. The optional parameter wait may be set to FALSE to
     * return immediately if this thread hasn't terminated yet.
     *
     * @param   bool wait default TRUE
     * @return  int status
     * @see     php://pcntl_waitpid
     */
    public function join($wait= TRUE) {
      if (0 == pcntl_waitpid($this->_id, $status, $wait ? WUNTRACED : WNOHANG)) return -1;
      $this->running= FALSE;
      $this->_id= $this->_pid= -1;
      return $status;
    }
    
    /**
     * Stop this thread
     *
     * @param   int signal default SIGINT
     * @throws  lang.IllegalThreadStateException
     */
    public function stop($signal= SIGINT) {
      if ($this->_id <= 0) {
        throw(new IllegalThreadStateException('Illegal thread id '.$this->_id));
      }
      posix_kill($this->_id, $signal);
      $this->running= FALSE;
      $this->_id= $this->_pid= -1;
    }
    
    /**
     * Returns thread id or -1 if this thread is not running
     *
     * @return  int
     */
    public function getId() {
      return $this->_id;
    }
    
    /**
     * Returns thread's parent id
     *
     * @return  int
     */
    public function getParentId() {
      return $this->_pid;
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf('%s[%d]@%s', $this->getClassName(), $this->_id, var_export($this, 1));
    }
    
    /**
     * Subclasses of Thread should override this method.
     *
     */
    public function run() { }
  }
?>
