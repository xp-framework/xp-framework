<?php
/* This class is part of the XP framework
 *
 * $Id: Binder.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.Injector', 'scriptlet.Session');

  /**
   * @purpose  Simple injection provider for a single predefined value.
   */
  class Binder extends Object {
    protected $injector;

    /**
     * Create a new binder
     *
     * @param  ioc.Injector $injector  optional
     */
    public function __construct(Injector $injector = NULL) {
      if (NULL === $injector) {
        $this->injector = new Injector();
      } else {
        $this->injector = $injector;
      }
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   scriptlet.Session  $session
     * @return  ioc.Binder
     */
    public function setSessionForSessionScope(Session $session) {
      $this->injector->setSessionForSessionScope($session);
      return $this;
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  ioc.ClassBinding
     */
    public function bind($interface) {
      return $this->injector->bind($interface);
    }

    /**
     * Bind a new constant
     *
     * @return  ioc.ConstantBinding
     */
    public function bindConstant() {
      return $this->injector->bindConstant();
    }

    /**
     * Get an injector for this binder
     *
     * @return  ioc.Injector
     */
    public function getInjector() {
      return $this->injector;
    }
  }
?>