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
  class AntProperty extends Object {
    public
      $name     = '',
      $value    = '';
    
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
    #[@xmlmapping(element= '@value')]
    public function setValue($value) {
      $this->value= $value;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function register(AntEnvironment $env) {
      $env->put($this->name, $this->value);
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function toString() {
      return $this->getClassName().'@('.$this->hashCode().') { '.$this->name.'= '.$this->value.' }';
    }    
  }
?>
