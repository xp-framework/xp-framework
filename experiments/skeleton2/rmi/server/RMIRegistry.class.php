<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Registry for RMI server
   *
   * Usage [with shared memory storage]:
   * <code>
   *   $registry= RMIRegistry::setup(new SharedMemoryStorage('HKEY_RMI'));
   *   $registry->register(new Test(), 'rmi.RMIObject');
   * </code>
   *
   * @see      xp://rmi.server.RMIServer
   * @purpose  Registry
   */
  class RMIRegistry extends Object {
    protected static $instance= NULL;
    public
      $storage  = NULL;
    
    /**
     * Set up storage
     *
     * @model   static
     * @access  public
     * @param   &util.registry.storage.RegistryStorage
     * @return  &rmi.RMIRegistry
     */  
    public static function setup($storage) {
      $instance= RMIRegistry::getInstance();
      
      // Initialize storage
      $storage->initialize();
      $instance->storage= $storage;

      return $instance;
    }
    
    /**
     * Finalize this registry (clear up all memory)
     *
     * @access  public
     */
    public function finalize() {
      $this->storage->free();
    }
    
    /**
     * Get registry instance
     *
     * @model   static
     * @access  public
     * @return  &rmi.RMIRegistry
     */  
    public static function getInstance() {
      if (!self::$instance) self::$instance= new RMIRegistry();
      return self::$instance;
    }
  
    /**
     * Get an RMI server object by its name
     *
     * @access  public
     * @param   string name
     * @return  &lang.Object or NULL if nothing is found
     */
    public function get($name) {
      if (!$this->storage->contains($name)) {
        return NULL;
      } else {
        return $this->storage->get($name);
      }
    }
    
    /**
     * Update the RMI server object
     *
     * @access  public
     * @param   string name
     * @param   &lang.Object object
     * @return  bool success
     */
    public function update($name, Object $object) {
      return $this->storage->put($name, $object);
    }
    
    /**
     * Register an RMI server object
     *
     * @access  public
     * @param   &lang.Object
     * @param   string name default NULL defaults to object->getClassName()
     * @return  bool success
     */
    public function register($object, $name= NULL) {
      return $this->storage->put(
        $name ? $name : $object->getClassName(),
        $object
      );
    }
  }
?>
