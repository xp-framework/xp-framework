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
  class AntUnknownTask extends AntTask {
    public
      $type     = '';

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '.', pass= array('name()'))]
    public function setType($type) {
      $this->type= $type;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function execute(AntEnvironment $env) {
      $env->out->writeLine('Unknown task ['.$this->type.'] invoked.');
    }    
  }
?>
