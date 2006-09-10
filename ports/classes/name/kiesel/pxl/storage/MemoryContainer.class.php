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
  class MemoryContainer extends Object {
    var
      $data=  array();
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($data= array()) {
      $this->data= $data;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function load($abstract) {
      return $this->data[$abstract];
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function save($abstract, $data) {
      $this->data[$abstract]= $data;
    }
  
  
  } implements(__FILE__, 'name.kiesel.pxl.storage.IStorage');
?>
