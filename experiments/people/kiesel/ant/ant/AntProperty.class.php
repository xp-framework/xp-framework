<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('ant.task.AntTask');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntProperty extends AntTask {
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
    protected function execute(AntEnvironment $env) {
      // Properties may be declared twice, first occurrence wins
      if ($env->exists($this->name)) return;
      $env->put($this->name, $env->substitute($this->value));
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
