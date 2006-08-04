<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.ElementNotFoundException');

  /**
   * Registry is basically a key/value-storage system. This class actually
   * acts as a proxy to the storage providers.
   *
   * Usage (setup):
   * <code>
   *   uses(
   *     'util.registry.Registry', 
   *     'util.registry.storage.SharedMemoryStorage',
   *     'util.Properties'
   *   );
   *
   *   $r= &Registry::setup('settings', new SharedMemoryStorage());
   *
   *   // Register properties if not already existant.
   *   if ($r->contains('database')) {
   *     $p= &new Properties('database.ini');
   *     $p->reset();
   *     $r->put('database', $config, 0600);
   *   }
   * </code>
   *
   * Usage (somewhere later on):
   * <code>
   *   $r= &Registry::getInstance('settings');
   *   $p= &$r->get('database');
   *
   *   // ... work with properties ...
   * </code>
   *
   * @purpose  Provide a mechanism to register any type of variable
   * @see      xp://util.registry.storage.RegistryStorage
   */ 
  class Registry extends Object {
    public
      $storage = NULL;

    /**
     * Return whether a given key exists
     *
     * @access  public
     * @param   string key
     * @return  bool TRUE when the key exists
     */
    public function contains($key) {
      return $this->storage->contains($key);
    }

    /**
     * Return all registered keys
     *
     * @access  public
     * @return  string[] key
     */
    public function keys() {
      return $this->storage->keys();
    }
    
    /**
     * Retrieve a value by a given key
     *
     * @access  public
     * @param   string key
     * @return  &mixed value
     */
    public function &get($key) {
      return $this->storage->get($key);
    }

    /**
     * Insert or update a key/value-pair
     *
     * @access  public
     * @param   string key
     * @param   &mixed value
     * @param   int permissions default 0666
     * @return  bool success
     */
    public function put($key, &$value, $permissions= 0666) {
      return $this->storage->put($key, $value, $permissions);
    }

    /**
     * Remove a value by a given key
     *
     * @access  public
     * @param   string key
     * @return  bool success
     */
    public function remove($key) {
      return $this->storage->remove($key);
    }

    /**
     * Private helper that simulates a static variable
     *
     * @access  private
     * @return  &util.registry.Registry[]
     */
    public function &_instances() {
      static $instances= array();
      return $instances;
    }

    /**
     * Setup this registry. You may call this method more than once to add
     * different storages. 
     *
     * For example, you may have a temporary storage that will simply go 
     * to shared memory, whereas (for assured persistance) you want to 
     * write another storage to the filesystem (or even a database). To
     * put this in code:
     *
     * <code>
     *   Registry::setup('temp', new SharedMemoryStorage());
     *   Registry::setup('persistent', new DBAStorage());
     * </code>
     *
     * @see     xp://util.registry.Registry#getInstance
     * @model   static
     * @access  public
     * @param   string name
     * @param   &util.registry.RegistryStorageProvider storage
     * @return  &util.registry.Registry registry object
     */
    public static function &setup($name, &$storage) {
      $instances= &Registry::_instances();
      $instances[$name]= new Registry();
      $instances[$name]->storage= &$storage;
      $instances[$name]->storage->initialize($name);

      return $instances[$name];
    }
    
    /**
     * Get registry instance by name. The name corresponds to the name that
     * was used for the setup() method.
     * 
     * @see     xp://util.registry.Registry#setup
     * @model   static
     * @access  public
     * @param   string name
     * @return  &util.registry.Registry registry object
     * @throws  lang.ElementNotFoundException if specified registry cannot be found
     */
    public static function &getInstance($name) {
      $instances= &Registry::_instances();
      if (!isset($instances[$name])) {
        throw(new ElementNotFoundException('No setup for '.$name));
      }
      return $instances[$name];
    }
  }
?>
