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
  abstract class AntTask extends Object {
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
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function needsToRun($env) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    abstract protected function execute(AntEnvironment $env);
    
            
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    final public function run(AntEnvironment $env) {
      if ($this->needsToRun($env)) {
        $env->out->writeLine('['.$this->getClassName().'] called.');
        
        $this->execute($env);
      }
    }
  }
?>
