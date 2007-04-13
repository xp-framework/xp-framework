<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('ant.AntEnvironment');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntProject extends Object {
    public
      $default      = NULL,
      $properties   = array(),
      $targets      = array();
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'property', class='ant.AntProperty')]
    public function addProperty($property) {
      $this->properties[]= $property;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'target', class='ant.AntTarget')]
    public function addTarget($target) {
      if (isset($this->targets[$target->getName()])) {
        throw new IllegalArgumentException('Target "'.$target->getName().'" is duplicate.');
      }
      
      $this->targets[$target->getName()]= $target;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@default')]
    public function setDefault($default) {
      $this->default= $default;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run($out, $err, $arguments) {
      if (sizeof($arguments) == 0) $arguments= array($this->default);
      
      $this->environment= new AntEnvironment($out, $err);
      
      $target= array_shift($arguments);
      if (!isset($this->targets[$target])) {
        throw new IllegalArgumentException('Target ['.$target.'] does not exist.');
      }
      
      $this->targets[$target]->run(clone $this->environment);
    }
  }
?>
