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
  class AntTask extends Object {
    public
      $id       = NULL,
      $taskname = NULL,
      $desc     = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@id')]
    public function setId($id) {
      $this->id= $id;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@taskname')]
    public function setTaskName($taskname) {
      $this->taskname= $taskname;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@description')]
    public function setDescription($desc) {
      $this->desc= $desc;
    }
  }
?>
