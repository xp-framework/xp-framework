<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ContainerManager extends Object {
    public
      $containers= array();
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function __construct() {
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function register(&$container) {
      $this->containers[]= &$container;
      $container->setContainerID(sizeof($this->containers)- 1);
      return sizeof($this->containers)- 1;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function getContainerByOID($oid) {
      // TBI
    }    
        
  }
?>
