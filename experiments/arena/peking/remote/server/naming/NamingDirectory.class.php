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
    public
      $_map   = NULL,
      $_cat   = NULL;
    
    protected static
      $instance = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->_map= new Hashmap();
      
      $log= Logger::getInstance();
      $this->_cat= $log->getCategory($this->getClassName());
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
      $this->_cat->info($this->getClassName(), 'binding new naming entry', $name);
      $this->_map->putref($name, $object);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function lookup($name) {
      if (!$this->_map->containsKey($name)) throw(new NameNotFoundException(
        $name.' not bound.'
      ));
      
      $this->_cat->debug($this->getClassName(), 'looked up naming entry', $name);
      return $this->_map->get($name);
    }
  }
?>
