<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('lang.Thread');

  // {{{ timer thread  
  class TimerThread extends Thread {
    protected
      $ticks    = 0,
      $timeout  = 0;

    // {{{ TimerThread __construct(int timeout)
    //     Constructor      
    public function __construct($timeout) {
      parent::__construct('timer.'.$timeout);
      $this->timeout= $timeout;
    }
    // }}}

    // {{{ void run(void)
    //     Thread runner implementation
    public function run() {
      Console::writeLinef('<%s> Start @ %s', $this->name, date('r'));
      while ($this->ticks < $this->timeout) {
        Thread::sleep(1000);
        $this->ticks++;
        Console::writeLinef('<%s> tick', $this->name);
      }
      Console::writeLinef('<%s> time\'s up @ %s', $this->name, date('r'));
    }
    // }}}
  }
  // }}}
  
  // {{{ main
  $t[0]= new TimerThread(5);
  $t[0]->start();
  $t[1]= new TimerThread(2);
  $t[1]->start();
  $t[0]->join();
  $t[1]->join();
  // }}}
?> 
