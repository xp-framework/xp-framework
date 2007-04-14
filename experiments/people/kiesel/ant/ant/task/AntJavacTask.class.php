<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'ant.task.AntTask',
    'lang.Process'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntJavacTask extends AntTask {
    public
      $srcdir   = NULL,
      $destdir  = NULL,
      $patternset = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public     
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@srcdir')]
    public function setSrcdir($dir) {
      $this->srcdir= $dir;
    }  
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function execute(AntEnvironment $env) {
      
    }  
  }
?>
