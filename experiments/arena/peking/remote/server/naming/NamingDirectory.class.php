<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Hashmap',
    'util.log.Logger',
    'remote.NameNotFoundException'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class NamingDirectory extends Object {
    protected
      $map   = NULL,
      $cat   = NULL;
    
    protected static
      $instance = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function __construct() {
      $this->map= new Hashmap();
      
      $this->cat= Logger::getInstance()->getCategory($this->getClassName());
    }
    
    static function __static() {
      self::$instance= new NamingDirectory();
    }
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function getInstance() {
      return self::$instance;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function bind($name, $object) {
      $this->cat->info($this->getClassName(), 'binding new naming entry', $name);
      $this->map->putref($name, $object);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function lookup($name) {
      if (!$this->map->containsKey($name)) throw new NameNotFoundException(
        $name.' not bound.'
      );
      
      $this->cat->debug($this->getClassName(), 'looked up naming entry', $name);
      return $this->map->get($name);
    }
  }
?>
