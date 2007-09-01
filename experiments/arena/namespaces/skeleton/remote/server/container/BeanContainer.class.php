<?php
/* This class is part of the XP framework
 *
 * $Id: BeanContainer.class.php 9300 2007-01-16 12:19:16Z kiesel $ 
 */

  namespace remote::server::container;

  uses(
    'lang.Collection', 
    'util.log.Traceable',
    'remote.server.container.BeanInjector'
  );

  /**
   * Bean container
   *
   * @purpose  abstract baseclass
   */
  abstract class BeanContainer extends lang::Object implements util::log::Traceable {
    public
      $instancePool = NULL;
    
    protected
      $cat            = NULL,
      $injector       = NULL,
      $configuration  = array();

    /**
     * Constructor
     *
     */
    protected function __construct() {
      $this->injector= new BeanInjector();
    }

    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Perform resource injection.
     *
     * @param   lang.Object instance
     */
    protected function inject($instance) {
      foreach ($instance->getClass()->getMethods() as $method) {
        if (!$method->hasAnnotation('inject')) continue;

        $inject= $method->getAnnotation('inject');
        $this->cat && $this->cat->info('---> Injecting', $inject['type'], 'via', $method->getName(TRUE).'()');
        
        $method->invoke($instance, array($this->injector->injectFor($inject['type'], $inject['name'])));
      }
    }

    /**
     * Get instance for class
     *
     * @param   lang.XPClass class
     * @return  remote.server.BeanContainer
     */
    public static function forClass($class) {
      $bc= new ();
      $bc->instancePool= lang::Collection::forClass($class->getName());
      return $bc;
    }

    /**
     * Invoke a method
     *
     * @param   lang.Object proxy
     * @param   string method
     * @param   mixed args
     * @return  mixed
     */
    public abstract function invoke($proxy, $method, $args);
  }
?>
