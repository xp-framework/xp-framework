<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('util.Properties');
  
  /**
   * Property-Manager
   * 
   * @purpose Verwaltet Property-Files
   * @access  static
   */
  class PropertyManager extends Object {
    var 
      $_path    = '.',
      $_prop    = array();
    
    /**
     * Instanz des Property-Managers zurückgeben
     * 
     * @see SingleTon#getInstance
     */
    function &getInstance() {
      static $PropertyManager__instance;
      
      if (!isset($PropertyManager__instance)) {
        $PropertyManager__instance= new PropertyManager();
      }
      return $PropertyManager__instance;
    }

    /**
     * Konfigurieren
     *
     * @access  public
     * @param   string path Suchpfad zu den Property-Files
     */
    function configure($path) {
      $this->_path= $path;
    }
    
    /**
     * Properties zurückgeben
     *
     * @access  public
     * @param   string name Property-File-Name
     * @return  util.Properties
     */
    function &getProperties($name) {
      if (!isset($this->_prop[$this->_path.$name])) {
        $this->_prop[$this->_path.$name]= &new Properties($this->_path.'/'.$name.'.ini');
      }
      return $this->_prop[$this->_path.$name];
    }
  }
?>
