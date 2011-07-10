<?php
/* This class is part of the XP framework
 *
 * $Id: DefaultInjectionProvider.class.php 3010 2011-02-15 13:54:23Z mikey $
 */

  uses(
    'ioc.InjectionProvider',
    'ioc.Injector'
  );

  /**
   * @purpose  Creates objects and injects all dependencies via the default injector.
   */
  class DefaultInjectionProvider extends Object implements InjectionProvider {
    protected
      $injector,
      $impl;

    /**
     * constructor
     *
     * @param  ioc.Injector  $injector
     * @param  lang.XPClass  $impl
     */
    public function __construct(Injector $injector, XPCLass $impl) {
      $this->injector = $injector;
      $this->impl     = $impl;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = NULL) {
      if ($this->impl->hasConstructor()) {
        $constructor = $this->impl->getConstructor();
      } else {
        $constructor = NULL;
      }
      
      if (NULL === $constructor || !$constructor->hasAnnotation('inject')) {
          $instance = $this->impl->newInstance();
      } else {
          $instance = $constructor->newInstance($this->injector->getInjectionValuesForMethod($constructor, $this->impl));
      }

      $this->injector->handleInjections($instance, $this->impl);
      return $instance;
    }
  }
?>