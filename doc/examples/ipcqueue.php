<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.sys.IPCQueue',
    'io.sys.Ftok', 
    'lang.Thread'
  );

  // {{{ sender thread
  class SenderThread extends Thread {
    public
      $num  = 0;

    // {{{ SenderThread __construct(int num)
    //     Constructor
    public function __construct($num) {
      parent::__construct('sender.'.$num);
      $this->num= $num;
      $this->queue= new IPCQueue(8925638);
    }
    // }}}

    // {{{ void run(void)
    //     Thread runner implementation
    public function run() {
      while ($this->sent < $this->num) {
        Thread::sleep(rand(100, 4000));
        Console::writeLinef('<%s> Sending message #%d @ %s', $this->name, $this->sent, date('r'));
        $this->queue->putMessage(new IPCMessage('Hello World ('.date('r').')'));
        $this->sent++;     
      }
      
      // Display sum
      Console::writeLinef(
        '<%s> Sent %d messages',
        $this->name, 
        $this->num
      );
      
      // Add message to signal receiver we've finished
      $this->queue->putMessage(new IPCMessage('__FINISH__'));
    }
    // }}}
  }
  // }}}
  
  // {{{ receiver thread
  class ReceiverThread extends Thread {

    // {{{ ReceiverThread __construct(void)
    //     Constructor
    public function __construct() {
      parent::__construct('receiver');
      $this->queue= new IPCQueue(8925638);
    }
    // }}}

    // {{{ void run(void)
    //     Thread runner implementation
    public function run() {
      do {

        // Wait for messages to arrive
        while (0 == $this->queue->getQuantity()) {
          Console::writeLinef('<%s> Sleeping...', $this->name);
          Thread::sleep(1000);
        }

        // Check how may messages there are
        Console::writeLinef(
          '<%s> There are %d messages in queue',
          $this->name,
          $this->queue->getQuantity()
        );

        // We have at least one message, read all
        while ($message= $this->queue->getMessage()) {
          Console::writeLinef(
            '<%s> Receiving message "%s"',
            $this->name, 
            $message->toString()
          );
          if ($message->getMessage() == '__FINISH__') break 2;
        }
      } while (1);
      
      // Remove queue
      $this->queue->removeQueue();
      Console::writeLinef('<%s> All messages received, queue removed.', $this->name);
    }
    // }}}
  }
  // }}}

  // {{{ main
  with ($s= new SenderThread(5), $r= new ReceiverThread()); {
    $s->start();
    $r->start();
    $s->join();  
    $r->join();
  }
  // }}}
?>
