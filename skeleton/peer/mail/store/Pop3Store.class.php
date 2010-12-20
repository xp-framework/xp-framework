<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('peer.mail.store.CclientStore');
  
  /**
   * Mail store
   *
   * @purpose  Wrap
   */
  class Pop3Store extends CclientStore {

    /**
     * Protected method to check whether this DSN is supported
     *
     * Supported notations:
     * <pre>
     * - pop3://localhost
     * - pop3://user:pass@localhost
     * - pop3://user@localhost:111
     * </pre>
     *
     * @param   peer.URL
     * @param   array attr
     * @param   int port
     * @return  bool
     * @throws  lang.IllegalArgumentException
     */
    protected function _supports($u, &$attr) {
      switch (strtolower($u->getScheme())) {
        case 'pop3': 
          $attr['proto']= 'pop3'; 
          $attr['port']= 110; 
          break;
          
        default: 
          return parent::_supports($u, $attr);
      }
      
      return TRUE;   
    }
  
  }
?>
