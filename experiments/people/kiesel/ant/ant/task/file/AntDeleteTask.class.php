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
  class AntDeleteTask extends AntTask {
    public
      $file             = NULL, 
      $dir              = NULL, 
      $verbose          = FALSE,
      $quiet            = FALSE,
      $failOnError      = TRUE,
      $includeEmptyDirs = FALSE,
      $fileset          = NULL;
      

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
     * Set dir
     *
     * @param   lang.Object dir
     */
    #[@xmlmapping(element= '@dir')]
    public function setDir($dir) {
      $this->dir= $dir;
    }

    /**
     * Set verbose
     *
     * @param   bool verbose
     */
    #[@xmlmapping(element= '@verbose')]
    public function setVerbose($verbose) {
      $this->verbose= ('true' == $verbose);
    }
    
    /**
     * Set quiet
     *
     * @param   bool quiet
     */
    #[@xmlmapping(element= '@quiet')]
    public function setQuiet($quiet) {
      $this->quiet= $quiet;
    }

    /**
     * Set failOnError
     *
     * @param   bool failOnError
     */
    #[@xmlmapping(element= '@failonerror')]
    public function setFailOnError($failOnError) {
      $this->failOnError= $failOnError;
    }

    /**
     * Set includeEmptyDirs
     *
     * @param   bool includeEmptyDirs
     */
    #[@xmlmapping(element= '@includeemptydirs')]
    public function setIncludeEmptyDirs($includeEmptyDirs) {
      $this->includeEmptyDirs= $includeEmptyDirs;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getFile(AntEnvironment $env) {
      return $env->localUri($env->substitute($this->file));
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getDir(AntEnvironment $env) {
      return $env->localUri($env->substitute($this->dir));
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function execute(AntEnvironment $env) {
      if (NULL !== $this->file) {
        $f= new File($this->getFile($env));
        if ($f->exists()) $f->unlink();
        
        return;
      } else if (NULL !== $this->dir) {
        $folder= new Folder($this->getDir($env));
        if ($folder->exists()) $folder->unlink();
        
        return;
      }
    }
  }
?>
