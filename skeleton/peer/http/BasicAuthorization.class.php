<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Header');

  /**
   * Basic Authorization
   *
   * @purpose  purpose
   */
  class BasicAuthorization extends Header {
    var 
      $user = '',
      $pass = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string user
     * @param   string pass
     */
    function __construct($user, $pass) {
      $this->user= $user;
      $this->pass= $pass;
      parent::__construct('Authorization', '');
    }
    
    /**
     * Get header value
     *
     * @access  public
     * @return  string value
     */
    function getValue() {
      return base64_encode($this->user.':'.$this->pass);
     }
  }
?>
