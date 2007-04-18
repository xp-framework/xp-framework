<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'ant.AntPatternSet',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntFileset extends Object {
    public
      $dir        = NULL,
      $file       = NULL,
      $patternset = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->patternset= new AntPatternSet();
    }
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@dir')]
    public function setDir($dir) {
      $this->dir= realpath($dir);
    }

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
    #[@xmlmapping(element= 'include', class= 'ant.AntPattern')]
    public function addIncludeRule($include) {
      $this->patternset->addIncludePattern($include);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@includes')]
    public function addSimpleIncludeRule($includes) {
      $this->patternset->addIncludePattern($includes);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'exclude', class= 'ant.AntPattern')]
    public function addExcludeRule($exclude) {
      $this->patternset->addExcludePattern($exclude);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@excludes')]
    public function addSimpleExcludeRule($excludes) {
      $this->patternset->addExcludePattern($excludes);
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'patternset', class= 'ant.AntPatternSet')]
    public function setPatternSet($set) {
      $this->patternset= $set;
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
    public function iteratorFor(AntEnvironment $env) {
      if (NULL == $this->dir) throw new IllegalStateException('No dir given in fileset');
      return new FilteredIOCollectionIterator(
        new FileCollection($this->getDir($env)),
        $this->patternset->createFilter($env, $this->getDir($env)),
        TRUE
      );
    }
  }
?>
