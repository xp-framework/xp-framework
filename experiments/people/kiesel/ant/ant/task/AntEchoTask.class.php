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
  class AntEchoTask extends AntTask {
    public
      $content= '';

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '.')]
    public function setContent($content) {
      $this->content= trim($content);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run(AntEnvironment $environment) {
      $environment->out->writeLine($this->content);
    }    
  }
?>
