<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('peer.mail.store.CclientStore');
  
  /**
   * Mail store
   *
   * @see
   * @purpose  Wrap
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
     * @access  protected
     * @param   array u
     * @param   &array attr
     * @param   &int port
     * @return  bool
     * @throws  IllegalArgumentException
     */
    function _supports($u, &$attr) {
      switch (strtolower($u['scheme'])) {
        case 'pop3': 
          $attr['mbx']= $u['path'];
          break;
          
        default: 
          return parent::_supports($u, $attr);
      }
      
      return TRUE;   
    }
  
  }
?>
