<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('security.SecureString', 'peer.http.HttpRequest');

  abstract class Authorization extends Object {
    protected $username;
    protected $password;

    /** @return string */
    public function username() { return $this->username; }

    /** @param string u */
    public function setUsername($u) { $this->username= $u; }
    
    /** @return security.SecureString */
    public function password() { return $this->password; }

    /** @param security.SecureString p */
    public function setPassword(SecureString $p) { $this->password= $p; }

    /**
     * Sign HTTP request
     * 
     * @param  peer.http.HttpRequest $request
     */
    abstract function sign(HttpRequest $request);

    public static function fromChallenge($header, $user, $pass) {
      raise('lang.MethodNotImplementedException', __METHOD__, 'Should be abstract');
    }
  }
?>