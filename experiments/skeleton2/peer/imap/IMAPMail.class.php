<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('peer.imap.IMAPClient');

  /**
   * @purpose Represents an imap mail
   * 
   * @ext           imap
   * @deprecated
   */
  class IMAPMail extends Object {
    const
      IMAP_RECENT = 0x0001,
      IMAP_UNSEEN = 0x0002,
      IMAP_ANWERED = 0x0004,
      IMAP_DELETED = 0x0008,
      IMAP_DRAFT = 0x0016,
      IMAP_FLAGGED = 0x0032;

    public
      $header,
      $body,
      $imapID;
      
    public 
      $imap;
      
    /**
     * Constructor
     *
     * @access public
     * @param array headers
     */
    public function __construct($id, $param) {
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
    public function setIMAP(&$imapConnection) {
      $this->imap= $imapConnection;
    }
    
    protected function _getHeader($header) {
      if (isset ($this->header->{$header}))
        return $this->header->{$header};
    
      return NULL;
    }
    
    public function getTo() {
      return self::_getHeader('to');
    }
    
    public function getFrom() {
      $from= self::_getHeader('from');
      return $from[0]->mailbox.'@'.$from[0]->host;
    }
    
    public function getFromAddress() {
      return imap_qprint (self::_getHeader('fromaddress'));
    }
    
    public function getCc() {
      return self::_getHeader('cc');
    }
    
    public function getSubject() {
      return imap_qprint (self::_getHeader('subject'));
    }
    
    public function getDate() {
        return self::_getHeader('udate');
    }
    
    public function getFlags() {
      $flag= 0;

      foreach (array ('Recent', 'Unseen', 
        'Answered', 'Deleted', 'Draft', 'Flagged') as $type=> $name) {
        
        if (' ' != $this->_getHeader ($name))
          $flag |= pow (2, $type);
      }
      
      return $flag;
    }
    
    public function getBody() {
      if (NULL === $this->body) {
        $this->body= $this->imap->_getImapBody($this->imapID);
      }
      return $this->body;
    }
    
    public function move($newFolder) {
      return $this->imap->_moveMail($this->imapID, $newFolder);
    }
    
    public function delete() {
      return $this->imap->_deleteMail($this->imapID);
    }
  
  }
?>
