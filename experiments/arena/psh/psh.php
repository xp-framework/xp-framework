<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File', 
    'io.sys.IPCQueue',
    'lang.Thread',
    'org.gnu.readline.ReadLine'
  );
  
  // {{{ CommandCompleter
  //     Completion function for readline
  class CommandCompleter extends Object {
  
    function complete($string, $offset, $length) {
      $func= get_defined_functions();
      $classes= array();
      foreach (xp::registry() as $key => $val) {
        if (0 != strncmp('class.', $key, 6)) continue;
        
        $classes[]= substr($val, strrpos($val, '.')+ 1);
      }
      return array_merge(
        array_keys(get_defined_constants()),
        $classes,
        $func['internal'],
        $func['user']
      );
    }
  } implements('CommandCompleter.class.php', 'org.gnu.readline.Completer');
  // }}}
  
  // {{{ ExecutorThread
  //     Thread that executes PHP code
  class ExecutorThread extends Thread {

    function __construct(&$comm) {
      parent::__construct('executor');
      $this->queue= &$comm;
    }
    
    function error($number, $message, $file, $line) {
      static $names= array(
        E_ERROR           => 'Error',
        E_WARNING         => 'Warning',
        E_PARSE           => 'Parse',
        E_NOTICE          => 'Notice',
        E_CORE_ERROR      => 'Core Error',
        E_CORE_WARNING    => 'Core Warning',
        E_COMPILE_ERROR   => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR      => 'User Error',
        E_USER_WARNING    => 'User Warning',
        E_USER_NOTICE     => 'User Notice',
      );
      
      Console::writeLinef(
        '*** %s: %s in %s:%d', 
        $names[$number], 
        $message, 
        $file, 
        $line
      );
    }

    function run() {
      set_error_handler(array(&$this, 'error'));
      while (1) {
        xp::gc();
        
        $message= &$this->queue->getMessage();
        try(); {
          eval($message->getMessage());
        } if (catch('Throwable', $e)) {
          $e->printStackTrace();
        }
        ob_flush();
      }
      restore_error_handler();
    }
  }
  // }}}
  
  // {{{ &lang.Thread spawn(&lang.Thread thread)
  //     Spawns a thread (starts it) and returns it
  function &spawn(&$thread) {
    $thread->start();
    return $thread;
  }
  // }}}
  
  // {{{ main
  $comm= &new IPCQueue(611214);
  $executor= &spawn(new ExecutorThread($comm));

  $history= &new File('.history');
  if ($history->exists()) {
    ReadLine::readHistoryFile($history);
  }  

  ReadLine::setCompleter(new CommandCompleter());
  while (FALSE !== ($line= ReadLine::readLn('$ '))) {
    if (0 == strlen(trim($line))) continue;
    
    $comm->putMessage(new IPCMessage($line));
    Console::writeLine();

    if (-1 != $executor->join($wait= FALSE)) {
      Console::writeLine('*** Executor has died, respawning');
      $executor= &spawn(new ExecutorThread($comm));
    }
  }
  $executor->stop();
  $comm->removeQueue();
  
  ReadLine::writeHistoryFile($history);
  Console::writeLine();
  // }}}
?>
