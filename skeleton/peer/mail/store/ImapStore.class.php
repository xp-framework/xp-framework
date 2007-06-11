<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('peer.mail.store.CclientStore');
 
  /**
   * Mail store
   *
   * @ext      imap
   * @see      php://imap
   * @purpose  Wrap
   */
  class ImapStore extends CclientStore {

    /**
     * Protected method to check whether this DSN is supported
     *
     * Supported notations:
     * <pre>
     * - imap://localhost
     * - imap://user:pass@localhost
     * - imap://user@localhost:143
     * - imaps://localhost:995/?novalidate-cert=1
     * </pre>
     *
     * @param   peer.URL u
     * @param   array attr
     * @return  bool
     * @throws  lang.IllegalArgumentException
     */
    protected function _supports($u, &$attr) {
      switch (strtolower($u->getScheme())) {
        case 'imap': 
          $attr['proto']= 'imap'; 
          $attr['port']= 143; 
          break;
          
        case 'imaps': 
          $attr['proto']= 'imap/ssl'.(empty($attr['novalidate-cert']) ? '' : '/novalidate-cert');
          $attr['port']= 993; 
          break;

        case 'imapt': 
          $attr['proto']= 'imap/tls'.(empty($attr['novalidate-cert']) ? '' : '/novalidate-cert');
          $attr['port']= 993; 
          break;
          
        default: 
          return parent::_supports($u, $attr);
      }
      
      return TRUE;   
    }
  }
?>
