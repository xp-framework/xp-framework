<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  define ('IMAP_RECENT',  0x0001);
  define ('IMAP_UNSEEN',  0x0002);
  define ('IMAP_ANWERED', 0x0004);
  define ('IMAP_DELETED', 0x0008);
  define ('IMAP_DRAFT',   0x0016);
  define ('IMAP_FLAGGED', 0x0032);

  uses ('peer.imap.IMAPClient');

  class IMAPMail extends Object {
    var
      $header,
      $body,
      $imapID;
      
    var 
      $imap;
      
    /**
     * Constructor
     *
     * @access public
     * @param array headers
     */
    function __construct($id, $param) {
      $this->imapID= $id;
      $this->header= $param;
      $this->body= NULL;
    }
    
    /**
     * set connection object
     *
     * @access public
     * @param IMAPClient objImap
     */
    function setIMAP(&$imapConnection) {
      $this->imap= &$imapConnection;
    }
    
    function _getHeader($header) {
      if (isset ($this->header->{$header}))
        return $this->header->{$header};
    
      return NULL;
    }
    
    function getTo() {
      return $this->_getHeader('to');
    }
    
    function getFrom() {
      $from= $this->_getHeader('from');
      return $from[0]->mailbox.'@'.$from[0]->host;
    }
    
    function getFromAddress() {
      return imap_qprint ($this->_getHeader('fromaddress'));
    }
    
    function getCc() {
      return $this->_getHeader('cc');
    }
    
    function getSubject() {
      return imap_qprint ($this->_getHeader('subject'));
    }
    
    function getDate() {
        return $this->_getHeader('udate');
    }
    
    function getFlags() {
      $flag= 0;

      foreach (array ('Recent', 'Unseen', 
        'Answered', 'Deleted', 'Draft', 'Flagged') as $type=> $name) {
        
        if (' ' != $this->_getHeader ($name))
          $flag |= pow (2, $type);
      }
      
      return $flag;
    }
    
    function getBody() {
      if (NULL === $this->body) {
        $this->body= $this->imap->_getImapBody($this->imapID);
      }
      return $this->body;
    }
    
    function move($newFolder) {
      return $this->imap->_moveMail($this->imapID, $newFolder);
    }
    
    function delete() {
      return $this->imap->_deleteMail($this->imapID);
    }
  
  }
