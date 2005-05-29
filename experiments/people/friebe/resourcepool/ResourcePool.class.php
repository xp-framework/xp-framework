<?php
/* This class is part of the XP framework's people's experiment
 *
 * $Id$ 
 */

  /**
   * Resouce Pool
   *
   * @see      rfc://0024
   * @purpose  Holds resources
   */
  class ResourcePool extends Object {
    var
      $resources  = array();

    /**
     * Bind the specified class to the resourcepool
     *
     * @model   static
     * @access  public
     * @param   &lang.Object instance
     */
    function bind(&$instance) {
      static $methods= array();
      
      $pool= &ResourcePool::getInstance();
      
      // Retrieve class methods with @inject annotation (and cache them)
      $key= $instance->getClassName();
      if (!isset($methods[$key])) {
        $class= &$instance->getClass();
        $methods[$key]= array();
        foreach ($class->getMethods() as $method) {
          if (!$method->hasAnnotation('inject')) continue;
          
          $methods[$key][$method->getName()]= $method->getAnnotation('inject', 'name');
        }
      }

      // Invoke injecters
      foreach ($methods[$key] as $method => $href) {
        if (!($resource= &$pool->lookup($href))) {
          return throw(new IllegalArgumentException('Unknown resource "'.$href.'"'));
        }
        $instance->{$method}($resource);
      }
    }

    /**
     * Gets an instance
     *
     * @model   static
     * @access  public
     * @return  &ResourcePool
     */
    function &getInstance() {
      static $instance= NULL;
      
      if (!$instance) $instance= new ResourcePool();
      return $instance;
    }
    
    /**
     * Register a resource
     *
     * @access  public
     * @param   string name
     * @param   &Resource resource
     * @return  &Resource
     */
    function &register($name, &$resource) {
      $this->resources[$name]= &$resource;
      return $resource;
    }
    
    /**
     * Lookup a resource. Returns a null-object in case the given name
     * is not found.
     *
     * @access  public
     * @param   string name
     * @return  &Resource
     */
    function &lookup($name) {
      if (!isset($this->resources[$name])) return xp::null();
      return $this->resources[$name];
    }
  }
?>
