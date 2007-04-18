<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'ant.AntPatternSet',
    'ant.AntFileIterator',
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
    #[@xmlmapping(element= '@defaultexcludes')]
    public function setDefaultExcludes($d) {
      $this->patternset->setDefaultExcludes($d);
    }
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@dir')]
    public function setDir($dir) {
      $this->dir= $dir;
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
    public function addIncludePattern($include) {
      $this->patternset->addIncludePattern($include);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@includes')]
    public function addIncludePatternString($includes) {
      $this->patternset->addIncludePattern($includes);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'exclude', class= 'ant.AntPattern')]
    public function addExcludePattern($exclude) {
      $this->patternset->addExcludePattern($exclude);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@excludes')]
    public function addExcludePatternString($excludes) {
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
      if (!($dir= $this->getDir($env))) throw new IllegalStateException('No directory given for fileset');
      $realdir= realpath($dir);
      if (!$realdir) throw new IllegalStateException('Direcotry "'.$dir.'" does not exist.');
      
      return new AntFileIterator(
        new FileCollection($realdir),
        $this->patternset->createFilter($env, $realdir),
        TRUE,
        $realdir
      );
    }
  }
?>
