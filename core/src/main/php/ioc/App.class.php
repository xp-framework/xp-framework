<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'ioc.Injector',
    'ioc.module.ArgumentsBindingModule',
    'ioc.module.BindingModule',
    'lang.IllegalArgumentException'
  );

  /**
   * @purpose  Class for starting the application by configuring the IoC container.
   */
  class App extends Object {

    /**
     * configures the application using the given binding modules and returns
     * injector so that the bootstrap file can request an instance of the entry
     * class
     *
     * @return  ioc.Injector
     */
    public static function createInjector() {
      return self::createInjectorWithBindings(self::extractArgs(func_get_args()));
    }

    /**
     * extracts arguments
     *
     * If arguments has only one value and this is an array this will be returned,
     * else all arguments will be returned.
     *
     * @param   string[]                             $args
     * @return  string[]|ioc.module.BindingModule[]
     */
    protected static function extractArgs($args) {
      if (count($args) === 1 && is_array($args[0])) {
          return $args[0];
      }

      return $args;
    }

    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @param   string    $className  full qualified class name of class to create an instance of
     * @param   string[]  $argv       optional  list of arguments
     * @return  object
     */
    public static function createInstance($className, $argv = null) {
      return self::createInjectorWithBindings(self::getBindingsForClass($className, $argv))
                 ->getInstance($className);
    }

    /**
     * creates list of bindings from given class
     *
     * @param   string                               $className  full qualified class name of class to retrieve bindings for
     * @param   string[]                             $argv       optional  list of arguments
     * @return  string[]|ioc.module.BindingModule[]
     */
    public static function getBindingsForClass($className, $argv = null) {
      $bindings = array();
      $class    = XPClass::forName($className);
      if (method_exists($class->getSimpleName(), '__bindings')) {
        $bindings = call_user_func(array($class->getSimpleName(), '__bindings'));
      }

      if (null !== $argv) {
        $bindings[] = new ArgumentsBindingModule($argv);
      }
 
      return $bindings;
    }

    /**
     * configures the application using the given binding modules and returns
     * injector so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   string[]|ioc.module.BindingModule[]  $bindingModules
     * @return  ioc.Injector
     */
    public static function createInjectorWithBindings($bindingModules) {
      return self::createBinderWithBindings($bindingModules)->getInjector();
    }

    /**
     * configures the application using the given binding modules and returns
     * binder so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   string[]|ioc.module.BindingModule[] $bindingModules
     * @return  ioc.Binder
     * @throws  lang.IllegalArgumentException
     */
    public static function createBinderWithBindings($bindingModules)
    {
      $binder = new Binder();
      foreach ($bindingModules as $bindingModule) {
        if (is_string($bindingModule)) {
          $bindingModule = XPClass::forName($bindingModule)->newInstance();
        }

        if (!($bindingModule instanceof BindingModule)) {
          throw new IllegalArgumentException('Given module class ' . get_class($bindingModule) . ' is not an instance of ioc.module.BindingModule');
        }

        $bindingModule->configure($binder);
      }

      // make injector itself available for injection
      $binder->bind('ioc.Injector')
             ->toInstance($binder->getInjector());
      return $binder;
    }
  }
?>