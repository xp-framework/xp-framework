<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.IllegalThreadStateException',
    'lang.SystemException'
  );

  /**
   * Thread
   *
   * <code>
   *   uses('lang.Thread');
   *   
   *   class TimerThread extends Thread {
   *     var
   *       $ticks    = 0,
   *       $timeout  = 0;
   *       
   *     function __construct($timeout) {
   *       $this->timeout= $timeout;
   *       parent::__construct('timer.'.$timeout);
   *     }
   *       
   *     function run() {
   *       while ($this->ticks < $this->timeout) {
   *         Thread::sleep(1000);
   *         $this->ticks++;
   *         printf("<%s> tick\n", $this->name);
   *       }
   *       printf("<%s> time's up!\n", $this->name);
   *     }
   *   }
   *   
   *   $t[0]= &new TimerThread(5);
   *   $t[0]->start();
   *   $t[1]= &new TimerThread(2);
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
   * @platform Unix
   * @purpose  Base class
   */
  class Thread extends Object {
    public
      $name     = '',
      $running  = FALSE;
      
    public
      $_id      = -1,
      $_pid     = -1;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    public function __construct($name= '') {
      $this->name= $name;
      
    }
    
    /**
     * Returns whether this thread is running
     *
     * @access  public
     * @return  bool
     */
    public function isRunning() {
      return $this->running;
    }
    
    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Causes the currently executing thread to sleep (temporarily cease 
     * execution) for the specified number of milliseconds. 
     *
     * @model   static
     * @access  public
     * @param   int millis
     */
    public static function sleep($millis) {
      usleep($millis * 1000);
    }

    /**
     * Starts thread execution
     *
     * @access  public
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
        $this->run();
        exit();
      }
    }
    
    /**
     * Join this thread. The optional parameter wait may be set to FALSE to
     * return immediately if this thread hasn't terminated yet.
     *
     * @access  public
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
     * @access  public
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
     * @access  public
     * @return  int
     */
    public function getId() {
      return $this->_id;
    }
    
    /**
     * Returns thread's parent id
     *
     * @access  public
     * @return  int
     */
    public function getParentId() {
      return $this->_pid;
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return sprintf('%s[%d]@%s', $this->getClassName(), $this->_id, var_export($this, 1));
    }
    
    /**
     * Subclasses of Thread should override this method.
     *
     * @model   abstract
     * @access  public
     */
    public function run() { }
  }
?>
