<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('name.kiesel.pxl.storage.IStorage');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MemoryContainer extends Object implements IStorage {
    public
      $data=  array();
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($data= array()) {
      $this->data= $data;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function load($abstract) {
      return $this->data[$abstract];
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function save($abstract, $data) {
      $this->data[$abstract]= $data;
    }
  
  
  } 
?>
