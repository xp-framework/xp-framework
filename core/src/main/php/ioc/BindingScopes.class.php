<?php
/* This class is part of the XP framework
 *
 * $Id: BindingScopes.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.SessionBindingScope', 'ioc.SingletonBindingScope');

  /**
   * Access to all built-in scopes.
   */
  class BindingScopes extends Object {
    protected
      $singletonScope,
      $sessionScope;

    /**
     * constructor
     *
     * @param  ioc.BindingScope  $singletonScope  optional
     * @param  ioc.BindingScope  $sessionScope    optional
     */
    public function  __construct(BindingScope $singletonScope = NULL, BindingScope $sessionScope = NULL) {
      if (NULL === $singletonScope) {
        $this->singletonScope = new SingletonBindingScope();
      } else {
        $this->singletonScope = $singletonScope;
      }

      if (NULL === $sessionScope) {
        $this->sessionScope = new SessionBindingScope();
      } else {
        $this->sessionScope = $sessionScope;
      }
    }

    /**
     * returns scope for singleton objects
     *
     * @return  ioc.BindingScope
     */
    public function getSingletonScope() {
      return $this->singletonScope;
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   scriptlet.Session $session
     * @return  ioc.BindingScope
     */
    public function setSessionForSessionScope(Session $session) {
      $this->sessionScope->setSession($session);
      return $this;
    }

    /**
     * returns scope for session resources
     *
     * @return  ioc.BindingScope
     */
    public function getSessionScope() {
      return $this->sessionScope;
    }
  }
?>