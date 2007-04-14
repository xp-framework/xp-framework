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
  class AntPattern extends Object {
    public
      $name   = NULL,
      $if     = NULL,
      $unless = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($name= NULL) {
      $this->name= $name;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@name')]
    public function setName($name) {
      $this->name= $name;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@if')]
    public function setIf($if) {
      $this->if= $if;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@unless')]
    public function setUnless($unless) {
      $this->unless= $unless;
    }
  }
?>
