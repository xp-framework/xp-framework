<?php
/* This class is part of the XP framework
 *
 * $Id: SessionBindingScope.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.BindingScope', 'scriptlet.Session', 'lang.RuntimeError');

  /**
   * Scope which ensures always the same instance for a class is provided within a session.
   */
  class SessionBindingScope extends Object implements BindingScope {
    const
      SESSION_KEY = 'ioc.sessionScope#';

    protected
      $session   = NULL,
      $instances = array();

    /**
     * sets the session
     *
     * @param  Session  $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * returns the requested instance from the scope
     *
     * @param   lang.XPClass  $type  type of the object
     * @param   lang.XPClass  $impl  concrete implementation
     * @param   ioc.InjectionProvider  $provider
     * @return  object
     * @throws  lang.RuntimeError
     */
    public function getInstance(XPClass $type, XPClass $impl, InjectionProvider $provider) {
      if (NULL === $this->session) {
        throw new RuntimeError('No instance of scriptlet.Session available.');
      }

      $key = self::SESSION_KEY . $impl->getName();
      if (isset($this->instances[$key])) {
        return $this->instances[$key];
      }

      if ($this->session->hasValue($key)) {
        $this->instances[$key] = $this->session->getValue($key);
        return $this->instances[$key];
      }

      $this->instances[$key] = $provider->get();
      $this->session->putValue($key, $this->instances[$key]);
      return $this->instances[$key];
    }
  }
?>