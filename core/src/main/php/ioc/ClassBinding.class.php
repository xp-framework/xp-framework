<?php
/* This class is part of the XP framework
 *
 * $Id: ClassBinding.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses(
    'ioc.Binding',
    'ioc.BindingException',
    'ioc.BindingScope',
    'ioc.BindingScopes',
    'ioc.DefaultInjectionProvider',
    'ioc.InjectionProvider',
    'ioc.Injector',
    'lang.IllegalArgumentException'
  );

  /**
   * Binding to bind an interface to an implementation.
   *
   * Please note that you can do a binding to a class or to an instance, or to
   * an injection provider, or to an injection provider class. These options are
   * mutually exclusive and have a predictive order:
   * 1. Instance
   * 2. Provider instance
   * 3. Provider class
   * 4. Concrete implementation class
   */
  class ClassBinding extends Object implements Binding {
    protected
      $injector      = NULL,
      $type          = NULL,
      $impl          = NULL,
      $name          = NULL,
      $scope         = NULL,
      $instance      = NULL,
      $provider      = NULL,
      $providerClass = NULL,
      $scopes        = NULL;

    /**
     * constructor
     *
     * @param  ioc.Injector  $injector
     * @param  string  $type
     * @param  ioc.BindingScopes  $scopes
     */
    public function __construct(Injector $injector, $type, BindingScopes $scopes) {
      $this->injector = $injector;
      $this->type     = $type;
      $this->impl     = $type;
      $this->scopes   = $scopes;
    }

    /**
     * set the concrete implementation
     *
     * @param   lang.XPClass|string  $impl
     * @return  ioc.ClassBinding
     * @throws  lang.IllegalArgumentException
     */
    public function to($impl) {
      if (!is_string($impl) && !($impl instanceof XPClass)) {
        throw new IllegalArgumentException('$impl must be a string or an instance of lang.XPClass');
      }

      $this->impl = $impl;
      return $this;
    }

    /**
     * set the concrete instance
     *
     * This cannot be used in conjuction with the 'toProvider()' or
     * 'toProviderClass()' method.
     *
     * @param   lang.Generic            $instance
     * @return  ioc.ClassBinding
     * @throws  lang.IllegalArgumentException
     */
    public function toInstance(Generic $instance) {
      if (!is($this->type, $instance)) {
        throw new IllegalArgumentException('Instance of ' . $this->type . ' expectected, ' . $instance->getClassName() . ' given.');
      }

      $this->instance = $instance;
      return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProviderClass()' method.
     *
     * @param   ioc.InjectionProvider  $provider
     * @return  ioc.ClassBinding
     */
    public function toProvider(InjectionProvider $provider) {
      $this->provider = $provider;
      return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProvider()' method.
     *
     * @param   string|lang.XPClass  $providerClass
     * @return  stubClassBinding
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
     * binds the class to the singleton scope
     *
     * @return  ioc.ClassBinding
     */
    public function asSingleton() {
      $this->scope = $this->scopes->getSingletonScope();
      return $this;
    }

    /**
     * binds the class to the session scope
     *
     * @return  ioc.ClassBinding
     */
    public function inSession() {
      $this->scope = $this->scopes->getSessionScope();
      return $this;
    }

    /**
     * set the scope
     *
     * @param   ioc.BindingScope  $scope
     * @return  ioc.ClassBinding
     */
    public function in(BindingScope $scope) {
      $this->scope = $scope;
      return $this;
    }

    /**
     * Set the name of the injection
     *
     * @param   string            $name
     * @return  ioc.ClassBinding
     */
    public function named($name) {
      $this->name = $name;
      return $this;
    }

    /**
     * returns the created instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     * @throws  ioc.BindingException
     */
    public function getInstance($type, $name)
    {
        if (NULL !== $this->instance) {
            return $this->instance;
        }

        if (is_string($this->impl)) {
            $this->impl = XPClass::forName($this->impl);
        }

        if (NULL === $this->scope) {
            if ($this->impl->hasAnnotation('singleton')) {
                $this->scope = $this->scopes->getSingletonScope();
            }
        }

        if (NULL === $this->provider) {
            if (NULL != $this->providerClass) {
                $provider = $this->injector->getInstance($this->providerClass);
                if (!($provider instanceof InjectionProvider)) {
                    throw new BindingException('Configured provider class ' . $this->providerClass . ' for type ' . $this->type . ' is not an instance of ioc.InjectionProvider.');
                }

                $this->provider = $provider;
            } else {
                $this->provider = new DefaultInjectionProvider($this->injector, $this->impl);
            }
        }

        if (NULL !== $this->scope) {
            return $this->scope->getInstance(XPClass::forName($this->type), $this->impl, $this->provider);
        }

        return $this->provider->get($name);
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey() {
      if (NULL === $this->name) {
        return $this->type;
      }

      return $this->type . '#' . $this->name;
    }
  }
?>