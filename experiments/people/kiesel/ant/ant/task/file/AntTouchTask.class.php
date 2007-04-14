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
  class AntTouchTask extends AntTask {
    public
      $file     = NULL,
      $datetime = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@file')]
    public function setFile($file) {
      $this->file= $file;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@datetime')]
    public function setDatetime($time) {
      $this->datetime= Date::fromString($time);
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@mkdirs')]
    public function setMkdirs($mkdirs) {
      $this->mkdirs= ($mkdirs == 'true');
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function execute(AntEnvironment $env) {
      if (!$this->datetime) {
        $this->datetime= Date::now();
      }
      
      $f= new File($this->file);
      $f->touch($this->datetime->getTime());
    }
  }
?>
