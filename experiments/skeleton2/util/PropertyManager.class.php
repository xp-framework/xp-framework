<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties');

  /**
   * Property-Manager
   * 
   * Usage:
   * <code>
   *   PropertyManager::getInstance()->configure('etc');
   *
   *   // ... later on ...
   *   $prop= PropertyManager::getInstance()->getProperties('database');
   *  
   *   // $prop is now a util.Property object with the properties
   *   // from etc/database.ini
   * </code>
   *
   * @purpose  Container
   */
  class PropertyManager extends Object {
    protected static $instance= NULL;
    protected
      $_path    = '.',
      $_prop    = array();
    
    /**
     * Retrieve this property manager's instance
     * 
     * @model   static
     * @access  public
     * @return  &util.PropertyManager
     */
    public static function getInstance() {
      if (!isset(self::$instance)) self::$instance= new PropertyManager();
      return self::$instance;
    }

    /**
     * Configure this property manager
     *
     * @access  public
     * @param   string path search path to the property files
     */
    public function configure($path) {
      $this->_path= $path;
    }
    
    /**
     * Return properties by name
     *
     * @access  public
     * @param   string name
     * @return  &util.Properties
     */
    public function getProperties($name) {
      if (!isset($this->_prop[$this->_path.$name])) {
        $this->_prop[$this->_path.$name]= new Properties(
          $this->_path.DIRECTORY_SEPARATOR.$name.'.ini'
        );
      }
      return $this->_prop[$this->_path.$name];
    }
  }
?>
