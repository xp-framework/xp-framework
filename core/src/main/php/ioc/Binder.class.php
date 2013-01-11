<?php
/* This class is part of the XP framework
 *
 * $Id: Binder.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.Injector', 'ioc.BindingScope');

  /**
   * Entry class to ease the adding of bindings.
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
     * sets session scope
     *
     * @param   ioc.BindingScope  $sessionScope
     * @return  ioc.Binder
     */
    public function setSessionScope(BindingScope $sessionScope) {
      $this->injector->setSessionScope($sessionScope);
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
     * Bind a new constant with given name
     *
     * @param   string  $name
     * @return  ioc.ConstantBinding
     */
    public function bindNamedConstant($name) {
      return $this->injector->bindNamedConstant($name);
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