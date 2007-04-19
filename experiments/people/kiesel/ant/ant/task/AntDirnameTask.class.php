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
  class AntDirnameTask extends AntTask {
    public
      $file     = NULL,
      $property = NULL;

    /**
     * Set file
     *
     * @param   lang.Object file
     */
    #[@xmlmapping(element= '@file')]
    public function setFile($file) {
      $this->file= $file;
    }

    /**
     * Set property
     *
     * @param   lang.Object property
     */
    #[@xmlmapping(element= '@property')]
    public function setProperty($property) {
      $this->property= $property;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function execute(AntEnvironment $env) {
      if (!$this->property || !$this->file)
        throw new IllegalArgumentException('Dirname must have property and file attribute');
      
      $env->put($this->property, dirname($this->file));
    }
  }
?>
