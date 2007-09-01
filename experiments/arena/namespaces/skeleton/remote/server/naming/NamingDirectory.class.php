<?php
/* This class is part of the XP framework
 *
 * $Id: NamingDirectory.class.php 9302 2007-01-16 17:01:53Z kiesel $ 
 */

  namespace remote::server::naming;

  uses(
    'util.Hashmap',
    'util.log.Logger',
    'remote.NameNotFoundException'
  );

  /**
   * Naming Directory. Provides lookup methods
   *
   * @purpose  Naming directory
   */
  class NamingDirectory extends lang::Object {
    protected
      $map   = NULL,
      $cat   = NULL;
    
    protected static
      $instance = NULL;

    /**
     * Constructor
     *
     */
    protected function __construct() {
      $this->map= new util::Hashmap();
      
      $this->cat= util::log::Logger::getInstance()->getCategory($this->getClassName());
    }
    
    static function __static() {
      self::$instance= new NamingDirectory();
    }
      
    /**
     * Get instance
     *
     * @return  remote.server.naming.NamingDirectory
     */
    public static function getInstance() {
      return self::$instance;
    }
    
    /**
     * Bind a name with an object
     *
     * @param   string name
     * @param   lang.Object object
     */
    public function bind($name, $object) {
      $this->cat->info($this->getClassName(), 'binding new naming entry', $name);
      $this->map->putref($name, $object);
    }
    
    /**
     * Look up object by its bound name
     *
     * @param   string name
     * @return  lang.Object
     * @throws  remote.NameNotFoundException if name has not been bound
     */
    public function lookup($name) {
      if (!$this->map->containsKey($name)) throw new remote::NameNotFoundException(
        $name.' not bound.'
      );
      
      $this->cat->debug($this->getClassName(), 'looked up naming entry', $name);
      return $this->map->get($name);
    }
  }
?>
