<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses( 
    'remote.server.container.BeanContainer',
    'lang.Collection',
    'util.PropertyManager',
    'util.log.Logger',
    'rdbms.ConnectionManager'
  );

  /**
   * Bean container
   *
   * @purpose  abstract baseclass
   */
  class StatelessSessionBeanContainer extends BeanContainer {

    /**
     * Get instance for class
     *
     * @param   lang.XPClass class
     * @return  remote.server.BeanContainer
     */
    public static function forClass($class) {
      $bc= new StatelessSessionBeanContainer();
      $bc->instancePool= Collection::forClass($class->getName());
      
      // Fetch class' classloader to check for resources configured 
      // for the bean.
      $cl= $class->getClassLoader();

      // Try loading the well known resources, and remember if it exists
      $bc->configuration['log.ini']= $cl->providesResource('etc/log.ini');
      $bc->configuration['database.ini']= $cl->providesResource('etc/log.ini');
      
      $bc->configuration['cl']= $cl;
      return $bc;
    }
    
    /**
     * Prepare "environment" for invocation on bean method. Configures
     * the PropertyManager (always), the Logger (if log.ini has been provided
     * with the bean) and ConnectionManager (if database.ini has been provided
     * with the bean).
     *
     */
    protected function prepare() {
      if ($this->configuration['log.ini']) {
        Logger::getInstance()->configure(Properties::fromString(
          $this->configuration['cl']->getResource('etc/log.ini')
        ));
      }
      if ($this->configuration['database.ini']) {
        ConnectionManager::getInstance()->configure(Properties::fromString(
          $this->configuration['cl']->getResource('etc/database.ini')
        ));
      }
    }
    
    /**
     * Invoke a method
     *
     * @param   lang.Object proxy
     * @param   string method
     * @param   mixed args
     * @return  mixed
     */
    public function invoke($proxy, $method, $args) {
      $class= $this->instancePool->getElementClass();

      // Prepare environment
      $this->prepare();

      if ($this->instancePool->isEmpty()) {
      
        // Create particular bean instance and perform resource injection
        $instance= $class->newInstance();
        $this->inject($instance);

        $this->instancePool->add($instance);
      } else {
        $instance= $this->instancePool->get(0);
      }

      $m= $class->getMethod($method);
      $this->cat && $this->cat->debug('BeanContainer::invoke() ', $m->toString(), '(', $args, ')');

      return $m->invoke($instance, $args);
    }
  }
?>
