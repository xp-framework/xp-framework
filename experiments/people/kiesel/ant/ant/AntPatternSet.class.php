<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.NegationOfFilter'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntPatternSet extends Object {
    public
      $id       = NULL,
      $includes = array(),
      $excludes = array();
    
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
    #[@xmlmapping(element= '@refid')]
    public function setRefId($refid) {
      $this->refid= $refid;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'include', class= 'ant.AntPattern')]
    public function addIncludePattern($pattern) {
      if (is_string($pattern)) {
        $pattern= new AntPattern($pattern);
      }
      
      if (!$pattern instanceof AntPattern) 
        throw new IllegalArgumentException('Expecting AntPattern or string');
      
      $this->includes[]= $pattern;
    }
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'exclude', class= 'ant.AntPattern')]
    public function addExcludePattern($pattern) {
      if (is_string($pattern)) {
        $pattern= new AntPattern($pattern);
      }
      
      if (!$pattern instanceof AntPattern) {
        throw new IllegalArgumentException('Expecting AntPattern or string');
      }
      
      $this->excludes[]= $pattern;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function createFilter(AntEnvironment $env, $basedir) {
      
      $inc= $exc= array();
      foreach ($this->includes as $include) {
        if ($include->applies($env)) {
          $inc[]= $include->toFilter($basedir);
        }
      }
      
      foreach ($this->excludes as $exclude) {
        if ($exclude->applies($env)) {
          $exc[]= $exclude->toFilter($basedir);
        }
      }
      
      foreach ($env->getDefaultExcludes() as $exclude) {
        if ($exclude->applies($env)) {
          $exc[]= $exclude->toFilter($basedir);
        }
      }
      
      if (!sizeof($inc))
        throw new IllegalStateException('No positive filter has been given.');
      
      $filter= array(new AllOfFilter($inc));
      if (sizeof($exc)) {
        $filter[]= new NegationOfFilter(new AnyOfFilter($exc));
      }
      
      return new AllOfFilter($filter);
    }
  }
?>
