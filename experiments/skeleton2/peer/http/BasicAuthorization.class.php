<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Header');

  /**
   * Basic Authorization
   *
   * <quote>
   * "HTTP/1.0", includes the specification for a Basic Access
   * Authentication scheme. This scheme is not considered to be a secure
   * method of user authentication (unless used in conjunction with some
   * external secure system such as SSL), as the user name and
   * password are passed over the network as cleartext.
   * </quote>
   *
   * @see      http://www.owasp.org/downloads/http_authentication.txt
   * @see      rfc://2617 
   * @purpose  Basic Authorization header
   */
  class BasicAuthorization extends Header {
    public
      $user = '',
      $pass = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string user
     * @param   string pass
     */
    public function __construct($user, $pass) {
      $this->user= $user;
      $this->pass= $pass;
      parent::__construct('Authorization', 'Basic');
    }
    
    /**
     * Get header value representation
     *
     * @access  public
     * @return  string value
     */
    public function getValueRepresentation() {
      return $this->value.' '.base64_encode($this->user.':'.$this->pass);
    }
  }
?>
