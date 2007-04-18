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
  class AntCopyTask extends AntTask {
    public
      $file             = NULL,
      $toFile           = NULL,
      $toDir            = NULL,
      $overwrite        = FALSE,
      $flatten          = FALSE,
      $includeEmptyDirs = FALSE,
      $failOnError      = TRUE,
      $verbose          = FALSE,
      $resources        = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@file')]
    public function setFile($f) {
      $this->file= $f;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@toFile')]
    public function setToFile($f) {
      $this->toFile= $f;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@toDir')]
    public function setToDir($d) {
      $this->toDir= $d;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@overwrite')]
    public function setOverwrite($o) {
      $this->overwrite= ($o == 'true');
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@flatten')]
    public function setFlatten($f) {
      $this->flatten= ($f == 'true');
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'fileset', class= 'ant.AntFileset')]
    public function setFileSet($fileset) {
      $this->resource= $fileset;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function execute(AntEnvironment $env) {
      if (NULL !== $this->resource) {
        $iter= $this->resource->iteratorFor($env);
        while ($iter->hasNext()) {
          $element= $iter->next();
        }
      } else if (NULL !== $this->file) {
      
      }
    }    
  }
?>
