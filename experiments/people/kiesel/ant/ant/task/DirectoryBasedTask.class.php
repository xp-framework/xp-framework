<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'ant.task.AntTask',
    'ant.AntFileset'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  abstract class DirectoryBasedTask extends AntTask {
    public
      $fileset  = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->fileset= new AntFileset();
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'include', class= 'ant.AntPattern')]
    public function addIncludeRule($include) {
      $this->fileset->addIncludePattern($include);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@includes')]
    public function addIncludePatternString($includes) {
      $this->fileset->addIncludePatternString($includes);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'exclude', class= 'ant.AntPattern')]
    public function addExcludePattern($exclude) {
      $this->fileset->addExcludePattern($exclude);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@excludes')]
    public function addExcludePatternString($excludes) {
      $this->fileset->addExcludePatternString($excludes);
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'fileset', class= 'ant.AntFileset')]
    public function setFileset($set) {
      $this->fileset= $set;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@dir')]
    public function setDir($dir) {
      $this->fileset->setDir($dir);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function iteratorForFileset(AntEnvironment $env) {
      return $this->fileset->iteratorFor($env);
    }
  }
?>
