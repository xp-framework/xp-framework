<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.sys.Ftok',
    'io.IOException',
    'io.sys.IPCMessage'
  );
  
  define('IPC_QUEUE_PERM',  0666);
  define('IPC_MSG_MAXSIZE', 16384);
  
  /**
   * Send System V IPC messages
   * <quote>
   * Inter process messaging (IPC) is a great way to deal with communicating
   * threads. If you have forked processes, this could be a great way 
   * of passing out work to them.
   * </quote>
   *
   * Usage example [with threads]
   * <code>
   *   require('lang.base.php');
   *   xp::sapi('cli');
   *
   *   uses(
   *     'io.sys.IPCQueue',
   *     'io.sys.Ftok', 
   *     'lang.Thread'
   *   );
   *
   *   class senderThread extends Thread {
   *     var
   *       $num  = 0;
   *
   *     function __construct($num) {
   *       $this->num= $num;
   *       $this->queue= &new IPCQueue(8925638);
   *       parent::__construct('sender.'.$this->num);
   *     }
   *
   *     function run() {
   *       while ($this->sent < $this->num) {
   *         Thread::sleep(1000);
   *         $this->queue->putMessage(new IPCMessage('hello world'));
   *         $this->sent++;     
   *
   *       }
   *       Console::writeLinef(
   *         "<%s> sent %d messages\n",
   *         $this->name, $this->num
   *       );
   *     }
   *   }
   *
   *   class receiverThread extends Thread {
   *
   *     function __construct($name) {
   *       $this->queue= &new IPCQueue(8925638);
   *       parent::__construct('receiver');
   *     }
   *
   *     function run() {
   *
   *       while (0 == $this->queue->getQuantity()) {
   *         Console::writeLinef('<%s> Sleeping...', $this->name);
   *         Thread::sleep(1000);
   *       }
   *
   *       while ($message= $this->queue->getMessage()) {
   *
   *         Console::writeLinef(
   *           "<%s> receiving message:\n -> %s\n",
   *           $this->name, $message->getMessage()
   *         );
   *         Thread::sleep(1000);
   *       }
   *       Console::writeLinef(
   *         'There are %d messages in queue',
   *         $this->queue->getQuantity()
   *       );
   *       $this->queue->removeQueue();
   *       Console::writeLine("All messages received, queue removed.");
   *
   *     }
   *   }
   *
   *   $t[0]= &new senderThread(2);
   *   $t[0]->start();
   *   $t[1]= &new receiverThread();
   *   $t[1]->start();
   *   var_dump($t[0]->join(), $t[1]->join());
   * </code>
   *
   * @purpose  Send and receive System V IPC messages
   */
  class IPCQueue extends Object {
    var
      $key      = 0,
      $id       = 0,
      $stat     = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   int System V IPC keys default NULL
     */  
    function __construct($key= NULL) {
      $this->key= (NULL == $key) ? Ftok::get() : $key;
      $this->id= msg_get_queue($this->key, IPC_QUEUE_PERM);
      $this->stat= msg_stat_queue($this->id);
    }
    
    /**
     * Put a message into queue
     *
     * @access  public
     * @param   io.sys.IPCMessage msg
     * @param   bool serialize default TRUE
     * @param   bool blocking default TRUE
     * @throws  io.IOException
     */
    function putMessage($msg, $serialize= TRUE, $blocking= TRUE) {
      if (!msg_send($this->id, $msg->getType(), $msg->getMessage(), $serialize, $blocking, $err)) {
        return throw(new IOException('Message could not be send. Errorcode '.$err));
      }
    }
    
    /**
     * Get a message from queue
     *
     * @access  public
     * @param   int desired messagetype
     * @param   int flags
     * @param   int maxsize default IPC_MSG_MAXSIZE
     * @param   bool serialize default TRUE
     * @throws  io.IOException
     * @return  string message 
     */    
    function getMessage($desiredType= 0, $flags= 0, $maxSize= IPC_MSG_MAXSIZE, $serialize= TRUE) {
    
      // refresh queue
      $this->stat= msg_stat_queue($this->id);
      
      // is a message in queue ?
      if (0 == $this->stat['msg_qnum'] && ($flags & MSG_IPC_NOWAIT)) {
        return FALSE;
      }
      
      // t.b.d. handle message flags and message types
      // see http://de3.php.net/manual/en/function.msg-receive.php
      if (!msg_receive($this->id, $desiredType, $msgType, $maxSize, $msg, $serialize, $flags, $err)) {
        return throw(new IOException('Message could not be received. Errorcode '.$err));
      }
      return new IPCMessage($msg, $msgType);
    }


    /**
     * Remove a message queue
     *
     * @access  public
     * @throws  io.IOException
     */    
    function removeQueue() {
    
      // refresh queue
      $this->stat= msg_stat_queue($this->id);
      
      if (0 !== $this->stat['msg_qnum']) {
        return throw(new IOException('Queue cannot be removed. There are unreceived messages.'));
      }
      msg_remove_queue($this->key);
    }
    
    /**
     * Get OwnerUID
     *
     * @access  public
     * @return  int
     */
    function getOwnerUID() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_perm.uid'];
    }

    /**
     * Get OwnerGID
     *
     * @access  public
     * @return  int
     */
    function getOwnerGID() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_perm.gid'];
    }

    /**
     * Get Permissions
     *
     * @access  public
     * @return  int
     */
    function getPermissions() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_perm.mode'];
    }

    /**
     * Get SentTime
     *
     * @access  public
     * @return  int
     */
    function getSentTime() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_stime'];
    }

    /**
     * Get ReceivedTime
     *
     * @access  public
     * @return  int
     */
    function getReceivedTime() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_rtime'];
    }

    /**
     * Get ChangedTime
     *
     * @access  public
     * @return  int
     */
    function getChangedTime() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_ctime'];
    }

    /**
     * Get Quantity
     *
     * @access  public
     * @return  int
     */
    function getQuantity() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_qnum'];
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    function getSize() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['qbytes'];
    }

    /**
     * Get SentPID
     *
     * @access  public
     * @return  int
     */
    function getSentPID() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_lspid'];
    }

    /**
     * Get ReceivedPID
     *
     * @access  public
     * @return  int
     */
    function getReceivedPID() {
      $this->stat= msg_stat_queue($this->id);
      return $this->stat['msg_lrpid'];
    }

    /**
     * Get IPC message key
     *
     * @access  public
     * @return  int
     */
    function getKey() {
      return $this->key;
    }
    
  }
?>
