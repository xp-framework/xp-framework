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
   *     public function __construct($timeout) {
   *       parent::__construct('timer.'.$timeout);
   *       $this->timeout= $timeout;
   *     }
   *       
   *     public function run() {
   *       while ($this->ticks < $this->timeout) {
   *         self::sleep(1000);
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
   * @platform Unix
   * @purpose  Base class
   */
  class Thread extends Object {
    public
      $name     = '',
      $running  = FALSE;
      
    private
      $_id      = -1;
      
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
     * @param   string name
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
    public function sleep($millis) {
      usleep($millis * 1000);
    }

    /**
     * Starts thread execution
     *
     * @access  public
     * @throws  lang.IllegalThreadStateException
     * @throws  lang.SystemException
     */
    public function start() {
      if (self::isRunning()) {
        throw (new IllegalThreadStateException('Already running'));
      }

      $pid= pcntl_fork();
      if (-1 == $pid) {     // Cannot fork
        throw (new SystemException('Cannot fork'));
      } elseif ($pid) {     // Parent
        $this->running= TRUE;
        $this->_id= $pid;
      } else {              // Child
        self::run();
        exit();
      }
    }
    
    /**
     * Join this thread (wait for it to exit).
     *
     * @access  public
     * @return  int status
     * @see     php://pcntl_waitpid
     */
    public function join() {
      pcntl_waitpid($this->_id, $status, WUNTRACED);
      $this->running= FALSE;
      $this->_id= -1;
      return $status;
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
