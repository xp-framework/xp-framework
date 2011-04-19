<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'peer.mail.MessagingException',
    'peer.mail.store.StoreCache',
    'peer.mail.MailFolder'
  );
 
  /**
   * An abstract class that models a message store and its access protocol, 
   * for storing and retrieving messages. Subclasses provide actual 
   * implementations. 
   *
   * Usage [example with IMAP]:
   * <code>
   *   $stor= new ImapStore();
   *   try {
   *     $stor->connect('imap://user:pass@imap.example.com');
   *     if ($f= $stor->getFolder('INBOX')) {
   *       $f->open();
   *       $list= $f->getMessages(range(1, 4), 5);
   *     }
   *   } catch(XPException $e) {
   *     $e->printStackTrace();
   *     $f->close();
   *     $stor->close();
   *     exit();
   *   }
   * 
   *   for ($i= 0, $s= sizeof($list); $i < $s; $i++) {
   *     echo $list[$i]->toString();
   *   }
   *   $f->close();
   *   $stor->close();
   * </code>
   *
   * @see      xp://peer.mail.MailFolder
   * @purpose  Interface for different MailStores
   */
  class MailStore extends Object {
    public 
      $_hdl  = NULL,
      $cache = NULL;
     
    /**
     * Constructor
     *
     * @param   peer.mail.store.StoreCache cache default NULL
     */ 
    public function __construct($cache= NULL) {
      if (NULL === $cache) {
        $this->cache= new StoreCache();
      } else {
        $this->cache= $cache;
      }
      
    }
      
    /**
     * Connect to store
     *
     * @param   string dsn
     * @return  bool success
     */
    public function open($dsn) { }
    
    /**
     * Disconnect from store
     *
     * @return  bool success
     */
    public function close() { }
  
    /**
     * Get a folder
     *
     * @param   string name
     * @return  peer.mail.MailFolder
     */
    public function getFolder($name) { }
    
    /**
     * Get all folders
     *
     * @return  peer.mail.MailFolder[]
     */
    public function getFolders() { }

    /**
     * Open a folder
     *
     * @param   peer.mail.MailFolder f
     * @param   bool readonly default FALSE
     * @return  bool success
     */
    public function openFolder($f, $readonly= FALSE) { }
    
    /**
     * Close a folder
     *
     * @param   peer.mail.MailFolder f
     * @return  bool success
     */
    public function closeFolder($f) { }
    
    /**
     * Get messages in a folder
     *
     * @param   peer.mail.MailFolder f
     * @param   var* msgnums
     * @return  peer.mail.Message[]
     */
    public function getMessages($f) { }

    /**
     * Get number of messages in this folder
     *
     * @param   peer.mail.MailFolder f
     * @param   string attr one of "message", "recent" or "unseen"
     * @return  int
     */
    public function getMessageCount($f, $attr) { }
  }
?>
