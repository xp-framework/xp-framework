<?php
/* This class is part of the XP framework
 *
 * $Id: StatelessSessionBeanContainer.class.php 9300 2007-01-16 12:19:16Z kiesel $ 
 */

  namespace remote::server::container;

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
      $bc= new ();
      $bc->instancePool= lang::Collection::forClass($class->getName());
      
      // Fetch class' classloader to check for resources configured 
      // for the bean.
      $cl= $class->getClassLoader();
      
      // Re-configure PropertyManager to the etc/ directory in the bean
      $bc->configuration['PROPERTY_PATH']= 'xar://'.$cl->archive->getURI().'?etc';

      // Try loading the log.ini resource, and remember if it exists
      // TBI: Maybe add a hasResource() method to lang.ClassLoader?
      try {
        $cl->getResource('etc/log.ini');
        $bc->configuration['log.ini']= TRUE;
      } catch (lang::ElementNotFoundException $ignore) {
      }
      
      try {
        $cl->getResource('etc/database.ini');
        $bc->configuration['database.ini']= TRUE;
      } catch (lang::ElementNotFoundException $ignore) {
      }
        
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
      util::PropertyManager::getInstance()->configure($this->configuration['PROPERTY_PATH']);
      
      if (isset($this->configuration['log.ini'])) {
        util::log::Logger::getInstance()->configure(util::PropertyManager::getInstance()->getProperties('log'));
      }
      
      if (isset($this->configuration['database.ini'])) {
        rdbms::ConnectionManager::getInstance()->configure(util::PropertyManager::getInstance()->getProperties('database'));
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
