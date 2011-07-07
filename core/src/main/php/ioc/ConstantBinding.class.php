<?php
/* This class is part of the XP framework
 *
 * $Id: ConstantBinding.class.php 3081 2011-03-01 22:11:00Z mikey $
 */

  uses('ioc.Binding');

  /**
   * @purpose  Interface for for different scopes of a binding.
   */
  class ConstantBinding extends Object implements Binding {
    const
      TYPE = '__CONSTANT__';

    protected
      $injector      = null,
      $name          = null,
      $value         = null,
      $provider      = null,
      $providerClass = null;

    /**
     * constructor
     *
     * @param  ioc.Injector  $injector
     */
    public function __construct(Injector $injector) {
      $this->injector = $injector;
    }

    /**
     * set the constant value
     *
     * @param   mixed  $value
     * @return  ioc.ConstantBinding
     */
    public function to($value) {
      $this->value = $value;
      return $this;
    }

    /**
     * set the name of the injection
     *
     * @param   string  $name
     * @return  ioc.ConstantBinding
     */
    public function named($name) {
      $this->name = $name;
      return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'to()' or
     * 'toProviderClass()' method.
     *
     * @param   ioc.InjectionProvider  $provider
     * @return  ioc.ConstantBinding
     */
    public function toProvider(InjectionProvider $provider) {
      $this->provider = $provider;
      return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'to()' or
     * 'toProvider()' method.
     *
     * @param   string|sXPClass  $providerClass
     * @return  ioc.ConstantBinding
     */
    public function toProviderClass($providerClass) {
      if ($providerClass instanceof XPClass) {
        $this->providerClass = $providerClass->getName();
      } else {
        $this->providerClass = $providerClass;
      }

      return $this;
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey() {
      return self::TYPE . '#' . $this->name;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     * @throws  ioc.BindingException
     */
    public function getInstance($type, $name) {
      if (null !== $this->provider) {
        return $this->provider->get($name);
      }

      if (null != $this->providerClass) {
        $provider = $this->injector->getInstance($this->providerClass);
        if (!($provider instanceof InjectionProvider)) {
          throw new BindingException('Configured provider class ' . $this->providerClass . ' for constant ' . $this->name . ' is not an instance of ioc.InjectionProvider.');
        }

        $this->provider = $provider;
        return $this->provider->get($name);
      }

      return $this->value;
    }
  }
?>