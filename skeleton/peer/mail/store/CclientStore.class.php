<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('peer.URL', 'peer.mail.store.MailStore');
 
  /**
   * Mail store
   *
   * @ext      imap
   * @see      php://imap
   * @purpose  Wrap
   */
  class CclientStore extends MailStore {
    var
      $currentfolder= NULL;
      
    /**
     * Return last errors/alerts
     *
     * @access  private
     * @return  string
     */
    function _errors() {
      return sprintf(
        'Alerts {%s} | Errors {%s}',
        @implode(', ', imap_alerts()),
        @implode(', ', imap_errors())
      );
    }
    
    /**
     * Protected method to check whether this DSN is supported
     *
     * @access  protected
     * @param   &peer.URL u
     * @param   &array attr
     * @param   &int port
     * @return  bool
     * @throws  IllegalArgumentException
     */
    function _supports(&$u, &$attr) {
      return throw(new IllegalArgumentException('Scheme "'.$u->getScheme().'" not recognized'));
    }
    
    /**
     * Connect to store using a DSN
     *
     * @access  public
     * @param   string dsn
     * @return  bool success
     * @see     php://imap_open
     * @throws  IllegalArgumentException in case scheme is not recognized
     * @throws  MessagingException
     */
    function connect($dsn) { 
      $attr= array();
      $flags= OP_HALFOPEN;

      // Parse DSN
      $u= &new URL($dsn);
      
      
      // DSN supported?
      if (FALSE === $this->_supports($u, $u->getParams())) return FALSE;

      // Read-only?
      if ($u->getParam('open')) $flags ^= OP_HALFOPEN;
      if ($u->getParam('read-only')) $flags |= OP_READONLY;
      
      $mbx= $u->getParam('mbx') ? $u->getParam('mbx') : sprintf(
        '{%s:%d/%s}',
        $u->getHost(),
        $u->getPort() ? $u->getPort() : $u->getParam('port'),
        $u->getParam('proto')
      );
      
      // Connect
      if (FALSE === ($conn= imap_open($mbx, @$u->getUser(), @$u->getPassword(), $flags))) {
        return throw(new MessagingException(
          'Connect to "'.$u->getUser().'@'.$mbx.'" failed',
          $this->_errors()
        ));
      }
      
      $this->_hdl= array($conn, $mbx);
      return TRUE;
    }
    
    /**
     * Disconnect from store
     *
     * @access  public
     * @return  bool success
     */
    function close() { 
      $r= imap_close($this->_hdl[0]);
      $this->_hdl[0]= NULL;
      return $r;
    }
    
    /**
     * Returns whether the connection is open
     *
     * @access  public
     * @return  bool
     */
    function isConnected() {
      return isset($this->_hdl[0]) && is_resource($this->_hdl[0]);
    }

    /**
     * Delete all messages marked for deletion
     *
     * @access  public
     * @return  bool success
     * @throws  MessagingException
     */    
    function expunge() {
      if (FALSE === imap_expunge($this->_hdl[0])) {
        return throw(new MessagingException(
          'Expunging deleted messages failed',
          $this->_errors()
        ));      
      }
      
      return TRUE;
    }
  
    /**
     * Get a folder. Note: Results from this method are cached.
     *
     * @access  public
     * @param   string name
     * @return  &peer.mail.MailFolder
     * @throws  MessagingException
     */
    function &getFolder($name) { 
      if (!$this->cache->has(SKEY_FOLDER.$name)) {
        if (FALSE === imap_list($this->_hdl[0], $this->_hdl[1], $name)) {
          trigger_error('Folder: '.$name, E_USER_NOTICE);
          return throw(new MessagingException(
            'Retrieving folder failed',
            $this->_errors()
          ));      
        }
        
        $folder= &new MailFolder($this, $name);
        $this->cache->put(SKEY_FOLDER.$name, $folder);
      } else {
        $folder= &$this->cache->get(SKEY_FOLDER.$name);
      }
      
      return $folder;
    }

    /**
     * Get all folders. Note: Results from this method are cached.
     *
     * @access  public
     * @return  &peer.mail.MailFolder
     * @throws  MessagingException
     */
    function &getFolders() {
      if (NULL === ($f= &$this->cache->get(SKEY_LIST.SKEY_FOLDER))) {
      
        // Retrieve list and cache it
        if (0 == ($s= sizeof($list= &imap_getmailboxes($this->_hdl[0], $this->_hdl[1], '*')))) {
          return throw(new MessagingException(
            'Retrieving folder list failed',
            $this->_errors()
          ));      
        }
        
        // Create MailFolder objects
        $f= array();
        $l= strlen($this->_hdl[1]);
        for ($i= 0; $i < $s; $i++) {
          $f[]= &new MailFolder(
            $this,
            imap_utf7_decode(substr($list[$i]->name, $l))
          );
        }

        $this->cache->put(SKEY_LIST.SKEY_FOLDER, $f);
      }
      
      return $f;
    }
  
    /**
     * Proxy method for MailFolder: Open a folder
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   bool readonly default FALSE
     * @return  bool success
     * @throws  MessagingException in case opening the folder failed
     * @throws  IllegalAccessException in case there is already a folder open
     */
    function openFolder(&$f, $readonly= FALSE) {
    
      // Is it already open?
      if ($this->currentfolder === $f->name) return TRUE;
      
      // Only one open folder at a time
      if (NULL !== $this->currentfolder) {
        trigger_error('Currently open Folder: '.$this->currentfolder, E_USER_NOTICE);
        return throw(new IllegalAccessException(
          'There can only be one open folder at a time. Close the currently open folder first.',
          $f->name
        ));      
      }
      
      // Try to reopen
      if (FALSE === imap_reopen(
        $this->_hdl[0], 
        $this->_hdl[1].$f->name, 
        $readonly ? OP_READONLY : 0
      )) {
        trigger_error('Folder: '.$name, E_USER_NOTICE);
        return throw(new MessagingException(
          'Opening folder failed',
          $this->_errors()
        ));      
      }
      
      // Success
      $this->currentfolder= $f->name;
      return TRUE;
    }
    
    /**
     * Proxy method for MailFolder: Close a folder
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @return  bool success
     */
    function closeFolder(&$f) { 
      $this->currentfolder= NULL;
      return TRUE;
    }

    /**
     * Proxy method for MailFolder: Get a message part
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   string uid
     * @param   string part
     * @return  string
     */
    function getMessagePart(&$f, $uid, $part) {
      return imap_fetchbody(
        $this->_hdl[0], 
        $uid, 
        $part, 
        FT_UID | FT_PEEK
      );
    }
    
    /**
     * Proxy method for MailFolder: Get message structure
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   string uid
     * @see     php://imap_fetchstructure
     * @return  &object
     */
    function &getMessageStruct(&$f, $uid) {
      return imap_fetchstructure(
        $this->_hdl[0], 
        $uid,
        FT_UID | FT_PEEK
      );
    }
    
    /**
     * Proxy method for MailFolder: Delete a message
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   &peer.mail.Message msg
     * @return  bool success
     */
    function deleteMessage(&$f, &$msg) {
      if (FALSE === imap_delete($this->_hdl[0], $msg->uid, FT_UID)) {
        trigger_error('UID: '.$msg->uid, E_USER_NOTICE);
        return throw(new MessagingException(
          'Setting flag \Deleted-flag for message failed',
          $this->_errors()
        ));
      }
      
      $msg->flags |= MAIL_FLAG_DELETED;
      return TRUE;
    }

    /**
     * Proxy method for MailFolder: Undelete a message
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   &peer.mail.Message msg
     * @return  bool success
     */
    function undeleteMessage(&$f, &$msg) {
      if (FALSE === imap_undelete($this->_hdl[0], $msg->uid, FT_UID)) {
        trigger_error('UID: '.$msg->uid, E_USER_NOTICE);
        return throw(new MessagingException(
          'Removing \Deleted-flag for message failed',
          $this->_errors()
        ));
      }
      
      $msg->flags |= ~MAIL_FLAG_DELETED;
      return TRUE;
    }
    
    /**
     * Proxy method for MailFolder: Move message to other folder
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   &peer.mail.Message msg
     * @return  bool success
     */
    function moveMessage(&$f, $msg) {
      if (FALSE === imap_mail_move($this->_hdl[0], $msg->uid, $f->name, CP_UID)) {
        return throw (new MessagingException('Can not move mail'));
      }
      
      return TRUE;
    }
    
    /**
     * Proxy method for MailFolder: Get messages in a folder
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   mixed* msgnums
     * @return  &peer.mail.Message[]
     * @throws  MessagingException
     */
    function &getMessages(&$f) {
      if (1 == func_num_args()) {
        $count= $this->getMessageCount($f, 'messages');
        $msgnums= range(1, $count);
      } else {
        $msgnums= array();
        for ($i= 1, $s= func_num_args(); $i < $s; $i++) {
          $arg= &func_get_arg($i);
          $msgnums= array_merge($msgnums, $arg);
        }
      }
      
      $messages= array();
      
      // Check cache
      $seq= '';
      foreach ($msgnums as $msgnum) {
        if (NULL === ($msg= &$this->cache->get(SKEY_LIST.SKEY_MESSAGE.$f->name.'.'.$msgnum))) {
          $seq.= ','.$msgnum;
        } else {
          $messages[]= &$msg;
        }
      }
      
      if (!empty($seq)) {
        if (FALSE === ($list= &imap_fetch_overview($this->_hdl[0], substr($seq, 1)))) {
          trigger_error('Folder: '.$f->name, E_USER_NOTICE);
          return throw(new MessagingException(
            'Reading messages {'.$seq.'} failed',
            $this->_errors()
          ));            
        }

        for ($i= 0, $s= sizeof($list); $i < $s; $i++) {
          $header= $this->getMessagePart($f, $list[$i]->uid, '0');
          $class= stristr($header, 'Content-Type: multipart/') ? 'MimeMessage': 'Message';
          
          $m= &new $class($list[$i]->uid);
          $m->size= $list[$i]->size;
          $m->folder= &$f;
          $m->body= NULL;   // Indicate this needs to be fetched

          // Flags
          if ($list[$i]->recent)   $m->flags |= MAIL_FLAG_RECENT;
          if ($list[$i]->flagged)  $m->flags |= MAIL_FLAG_FLAGGED;
          if ($list[$i]->recent)   $m->flags |= MAIL_FLAG_RECENT;
          if ($list[$i]->answered) $m->flags |= MAIL_FLAG_ANSWERED;
          if ($list[$i]->seen)     $m->flags |= MAIL_FLAG_SEEN;
          if ($list[$i]->deleted)  $m->flags |= MAIL_FLAG_DELETED;
          if ($list[$i]->draft)    $m->flags |= MAIL_FLAG_DRAFT;

          // Parse headers
          $m->setHeaderString($header);
          
          // Cache it
          $this->cache->put(SKEY_LIST.SKEY_MESSAGE.$f->name.'.'.$list[$i]->msgno, $m);

          $messages[]= &$m;
        }
      }
      
      return $messages;
    }

    /**
     * Proxy method for MailFolder: Get number of messages in this folder
     * Note: The results from this method are cached.
     *
     * @access  public
     * @param   &peer.mail.MailFolder f
     * @param   string attr one of "messages", "recent" or "unseen"
     * @return  int status
     */
    function getMessageCount(&$f, $attr) {
      if (NULL === ($info= $this->cache->get(SKEY_INFO.SKEY_FOLDER.$f->name))) {
        if (FALSE === ($info= imap_status(
          $this->_hdl[0], 
          $this->_hdl[1].$f->name, 
          SA_MESSAGES | SA_RECENT | SA_UNSEEN
        ))) {
          trigger_error('Folder: '.$f->name, E_USER_NOTICE);
          return throw(new MessagingException(
            'Retrieving message count [SA_'.strtoupper($attr).'] failed',
            $this->_errors()
          ));
        }            
      }
      
      return $info->$attr;
    }
  }
?>
