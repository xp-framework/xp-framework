<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'peer.mail.Message',
    'peer.mail.MimeMessage'
  );

  /**
   * Mail folder
   *
   * @purpose  Wrap
   */
  class MailFolder extends Object {
    public
      $name  = '',
      $store = NULL;
      
    public
      $_ofs  = 0;
    
    /**
     * Constructor
     *
     * @param   peer.mail.store.MailStore store
     * @param   string name default ''
     */  
    public function __construct($store, $name= '') {
      $this->name= $name;
      $this->store= $store;
      
    }
  
    /**
     * Create string representation, e.g.
     * <pre>
     * peer.mail.MailFolder[INBOX]@{
     *   name  -> peer.mail.store.ImapStore
     *   cache -> peer.mail.store.StoreCache[5]@{
     *     [folder/INBOX            ] object [mailfolder]
     *     [list/message/INBOX1     ] object [message]
     *     [list/message/INBOX2     ] object [message]
     *     [list/message/INBOX3     ] object [message]
     *     [list/message/INBOX5     ] object [message]
     *   }
     * }
     * </pre>
     *
     * @see     xp://peer.mail.store.StoreCache#toString
     * @return  string
     */
    public function toString() {
      return (
        $this->getClassName().
        '['.
        $this->name.
        "]@{\n  name  -> ".
        $this->store->getClassName().
        "\n  cache -> ".
        str_replace("\n", "\n  ", $this->store->cache->toString()).
        "\n}"
      );
    }
    
    /**
     * Open this folder
     *
     * @param   bool readonly default FALSE
     * @return  bool success
     */
    public function open($readonly= FALSE) { 
      $this->_ofs= 0;
      return $this->store->openFolder($this, $readonly);
    }

    /**
     * Close this folder
     *
     * @return  bool success
     */
    public function close() { 
      return $this->store->closeFolder($this);
    }
  
    /**
     * Get messages
     *
     * <code>
     *   // Get all messages
     *   $f->getMessages();
     *
     *   // Get messages #1, #4 and #5
     *   $f->getMessages(1, 4, 5);
     *
     *   // Get messages #3, #7 and #10 through #14
     *   $f->getMessages(3, 7, range(10, 14));
     * </code>
     *
     * @param   var* msgnums
     * @return  peer.mail.Message[]
     */
    public function getMessages() { 
      $args= func_get_args();
      array_unshift($args, $this);
      return call_user_func_array(array($this->store, 'getMessages'), $args);
    }
    
    /**
     * Rewind this folder (set the iterator offset for getMessage() to 0)
     *
     */
    public function rewind() {
      $this->_ofs= 0;
    }
    
    /**
     * Delete a message
     *
     * @param   peer.mail.Message msg
     * @return  bool success
     */
    public function deleteMessage($msg) {
      return $this->store->deleteMessage($this, $msg);
    }

    /**
     * Undelete a message
     *
     * @param   peer.mail.Message msg
     * @return  bool success
     */
    public function undeleteMessage($msg) {
      return $this->store->undeleteMessage($this, $msg);
    }
    
    /**
     * Move a message
     *
     * @param   peer.mail.Message msg
     * @return  bool success
     */
    public function moveMessage($msg) {
      return $this->store->moveMessage($this, $msg);
    }
    
    /**
     * Get next message (iterator)
     *
     * Example:
     * <code>
     *   $f->open();                           
     *   while ($msg= $f->getMessage()) {     
     *     echo $msg->toString();
     *   }                                     
     *   $f->close();                          
     * </code>
     *
     * @return  peer.mail.Message or FALSE to indicate we reached the last mail
     */
    public function getMessage() {
      $this->_ofs++;
      $ret= $this->store->getMessages($this, $this->_ofs);
      return $ret[0];
    }

    /**
     * Get a message part
     *
     * @param   string uid
     * @param   string part
     * @return  int
     */
    public function getMessagePart($uid, $part) { 
      return $this->store->getMessagePart($this, $uid, $part);
    }

    /**
     * Get a message structure
     *
     * @param   string uid
     * @return  object
     */
    public function getMessageStruct($uid) { 
      return $this->store->getMessageStruct($this, $uid);
    }

    /**
     * Get number of messages in this folder
     *
     * @return  int
     */
    public function getMessageCount() {
      return $this->store->getMessageCount($this, 'messages');
    }

    /**
     * Get number of new messages in this folder
     *
     * @return  int
     */
    public function getNewMessageCount() {
      return $this->store->getNewMessageCount($this, 'recent');
    }

    /**
     * Get number of unread messages in this folder
     *
     * @return  intGet number of messages in this folder
     */
    public function getUnreadMessageCount() {
      return $this->store->getUnreadMessageCount($this, 'unseen');
    }

  }
?>
