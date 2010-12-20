<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('peer.mail.store.CclientStore');
  
  /**
   * Mail store
   *
   * @see      xp://peer.mail.store.CclientStore
   * @purpose  Storage
   */
  class MboxStore extends CclientStore {

    /**
     * Protected method to check whether this DSN is supported
     *
     * Supported notations:
     * <pre>
     * - mbox:///usr/home/foo/Mail
     * </pre>
     *
     * @param   peer.URL u
     * @param   array attr
     * @return  bool
     * @throws  lang.IllegalArgumentException
     */
    protected function _supports($u, &$attr) {
      switch (strtolower($u->getScheme())) {
        case 'mbox': 
          $attr['mbx']= '/'.$u->getHost().$u->getPath();
          $attr['open']= TRUE;
          break;
          
        default: 
          return parent::_supports($u, $attr);
      }
      
      return TRUE;   
    }
  
    /**
     * Get a folder. Note: Results from this method are cached.
     *
     * @param   string name
     * @return  peer.mail.MailFolder
     * @throws  peer.mail.MessagingException
     */  
    public function getFolder() {
      return parent::getFolder('*');
    }
  }
?>
