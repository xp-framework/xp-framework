<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses(
    'peer.imap.IMAPClient',
    'peer.imap.IMAPException'
  );

  /**
   * @purpose   represents an IMAP folder
   *
   * @ext       imap
   * @deprecated
   *
   */
  class IMAPFolder extends Object {
    public
      $folderName,
      $activeMailbox;
      
    public 
      $msg,
      $ptr;
    
    public
      $imap;
      
    /**
     * create an IMAPFolder object
     *
     * @access public
     * @param string folderName
     * @param IMAPClient clt
     */
    public function __construct($folderName) {
      $this->folderName= $folderName;
      $this->activeMailbox= '';
      $this->imap= NULL;
      $this->ptr= NULL;
    }
    
    /**
     * fetches all headers
     * 
     * @access public
     * @return bool success
     */
    public function init() {
      if (!self::isActive() && !$this->open ())
        return false;
      
      $cntHeaders= $this->imap->_numMsg();
      if (NULL !== $this->msg) {
        for ($i= 0; $i< count ($this->msg); $i++) {
          unset ($this->msg[$i]);
        }
      }

      // Alle Message-Header holen
      $this->msg= array ();
      for ($i= 0; $i< $cntHeaders; $i++) {
        $hdr= $this->imap->_getHeader ($i+1);

        $mail= new IMAPMail($i+1, $hdr);
        $mail->setIMAP ($this->imap);

        $this->msg[$i]= $mail;
      }
    }
    
    /**
     * sets the imap object
     *
     * @access public
     * @param IMAPClient clt
     */
    public function setIMAP(&$imap) {
      $this->imap= $imap;
    }

    /**
     * Is this the active mailbox?
     *
     * @access public
     * @return bool isActive
     */    
    public function isActive() {
      return ($this->activeMailbox == $this->folderName);
    }
    
    /**
     * set new active mailbox
     *
     * @access public
     * @param string activeMailbox
     */
    public function setActiveMailbox($mbx) {
      $this->activeMailbox= $mbx;
    }
  
    /**
     * open this folder
     *
     * @access public
     * @return bool success
     */
    public function open() {
      return $this->imap->_openMailbox ($this->folderName);
    }
    
    /** 
     * iterate through mails
     *
     * @access public
     * @return IMAPMail obj
     */
    public function getNextMail() {
      if (NULL === $this->ptr)
        $this->ptr= 0;
        
      if (!isset ($this->msg[$this->ptr])) {
        $this->ptr= NULL;
        return NULL;
      }
      
      return $this->msg[$this->ptr++];
    }
    
    /**
     * commits all changes
     *
     * @access public
     * @return bool success
     */
    public function expunge() {
      if (!$this->isActive ()) {
        throw  (new IMAPException ('Inactive folder cannot be expunged'));
      }
      
      $retval= $this->imap->_expunge ();
      $this->init ();
      return $retval;
    }
  }
?>
