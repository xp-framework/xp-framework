<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('peer.imap.IMAPException');

  /**
   * IMAP Client
   *
   * Example
   * <code>
   *   $imap= new IMAPClient (array (
   *     'host' => 'imap.foo.bar',
   *     'user' => 'baz',
   *     'pass' => '**censored**'
   *   ));
   *   $imap->init();
   *   $inbox= $imap->getFolder('INBOX');
   *   $inbox->init ();
   *   while (NULL !== ($mail= $inbox->getNextMail ())) {
   *     echo sprintf ("%d - From: %20s %s\n",
   *       $mail->getFlags (),
   *       $mail->getFromAddress (),
   *       $mail->getSubject ()
   *     );
   *   }
   *  </code>
   *
   * @ext imap
   * @deprecated
   */   
  class IMAPClient extends Object {
    public
      $host,
      $mbx,
      $port,
      $proto,
      $user,
      $pass;
      
    public
      $_hdl;

    public 
      $mailbox;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string host default 'localhost' LDAP server
     * @param   array
     */
    public function __construct($params) {
      $this->proto= 'imap';
      $this->port= 143;
      $this->mbx= 'INBOX';
      $this->mailbox= array ();

      
    }
    
    /**
     * Create the IMAP reference string
     *
     * @access private
     * @return string ref
     */
    private function _getIMAPConnectionString() {
      return sprintf ('{%s:%d/%s}',
        $this->host,
        $this->port,
        $this->proto
      );
    }
    
    /**
     * returns IMAP folder string
     *
     * @access public
     * @param string foldername default NULL
     * @return string folderstring
     */
    public function _getIMAPFolderString($folderName= NULL) {
      if (NULL === $folderName) 
        $folderName= $this->mbx;
      
      return sprintf ('{%s:%d/%s}%s',
        $this->host,
        $this->port,
        $this->proto,
        $folderName
      );
    }
    
    /**
     * Connect to IMAP server
     *
     * @access public
     * @return IMAP resource handle
     */
    public function connect() {
        if (FALSE === ($this->_hdl= imap_open (
          self::_getIMAPConnectionString(), $this->user, $this->pass
        ))) {
          throw  (new IOException('Cannot connect to '.self::_getIMAPConnectionString()));
        }
        
        return $this->_hdl;
    }
    
    /**
     * Close IMAP connection
     *
     * @access public
     * @return bool success
     */
    public function close() {
      return imap_close ($this->_hdl);
    }
    
    /**
     * Load all mailbox names
     *
     * @access public
     * @return bool success
     */
    public function init() {
      if (!$this->_hdl)
        $this->connect ();
        
      $mailboxes= imap_list (
        $this->_hdl, 
        self::_getIMAPConnectionString(),
        '*'
      );
      
      foreach ($mailboxes as $mbx) {
        $this->mailbox[$mbx]= new IMAPFolder ($mbx);
        $this->mailbox[$mbx]->setIMAP ($this);
      }
      
      return true;
    }
    
    /**
     * Show all mailboxes
     *
     * @access public
     * @return array mailboxes
     */
    public function getMailboxes() {
      return array_keys ($this->mailbox);
    }
    
    public function existsMailbox($mbx) {
      return isset ($this->mailbox[$mbx]);
    }
    
    /**
     * Expunges the mailbox
     *
     * @access public
     * @return bool success
     *
     */
    public function _expunge() {
      return imap_expunge ($this->_hdl);
    }
    
    /**
     * fetches mail body
     *
     * @access public
     * @param int messageid
     * @return string body
     */
    public function _getImapBody($id) {
      return $this->imap_body ($this->_hdl, $id);
    }
    
    /** 
     * move mail to another folder
     *
     * @access public
     * @param int messageid
     * @param string newfolder
     * @return bool success
     */
    public function _moveMail($id, $folder) {
      $fqFoldername= $this->_getIMAPFolderString ($folder);
      if (!$this->existsMailbox ($fqFolderName)) {
        throw  (new IMAPException ('Mail cannot be moved to nonexistant folder'));
      }
      
      return imap_mail_move ($this->_hdl, $id, $folder);
    }
    
    /**
     * delete mail
     *
     * @access public
     * @param int messageid
     * @return bool success
     */
    public function _deleteMail($id) {
      return imap_delete ($this->_hdl, $id);
    }
    
    /**
     * return number of messages in active folder
     *
     * @access public
     * @return int count
     */
    public function _numMsg() {
      return imap_num_msg ($this->_hdl);
    }
    
    /**
     * get mail's headers
     *
     * @access public
     * @param int index
     * @return object header
     */    
    public function _getHeader($idx) {
      return imap_headerinfo ($this->_hdl, $idx);
    }
    
    /**
     * switch to new Folder, notify all mailboxes
     *
     * @access public
     * @param string newfolder
     * @return bool success
     */
    public function _openMailbox($newMailbox) {
      // Does this mailbox exists?
      if (!$this->existsMailbox ($newMailbox)) {
        throw  (new IMAPException ('Mailbox does not exist: '.$newMailbox));
      }
      
      $this->mbx= $newMailbox;
      if (true === imap_reopen ($this->_hdl, $newMailbox)) {
        // Notify mailboxes about folder change
        foreach ($this->mailbox as $name=> $mbx) {
          $this->mailbox[$name]->setActiveMailbox ($newMailbox);
        }
        
        return true;
      } else 
        return false;
    }
    
    /**
     * returns folder object
     *
     * @access public
     * @param string folder
     * @return IMAPFolder folder
     */
    public function getFolder($folderName) {
      $folderName= $this->_getIMAPFolderString ($folderName);
      
      if (!$this->existsMailbox ($folderName))
        return NULL;
      
      return $this->mailbox[$folderName];
    }
  }
?>
