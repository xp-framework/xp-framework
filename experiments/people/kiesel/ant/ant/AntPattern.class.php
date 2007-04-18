<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('ant.TopURIMatchesFilter');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntPattern extends Object {
    public
      $name   = NULL,
      $if     = NULL,
      $unless = NULL;
    
    protected
      $directorySeparator=  DIRECTORY_SEPARATOR;
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($name= NULL, $ds= DIRECTORY_SEPARATOR) {
      $this->name= $name;
      $this->directorySeparator= $ds;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setDirectorySeparator($ds) {
      $this->directorySeparator= $ds;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@name')]
    public function setName($name) {
      $this->name= $name;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@if')]
    public function setIf($if) {
      $this->if= $if;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@unless')]
    public function setUnless($unless) {
      $this->unless= $unless;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function applies(AntEnvironment $env) {
      if (NULL !== $this->if) {
        return $env->exists($this->if);
      }
      
      if (NULL !== $this->unless) {
        return !$env->exists($this->unless);
      }
      
      // Otherwise always applies
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function nameToRegex() {
      
      // From the ant manual:
      // if a pattern ends with / or \, then ** is appended. 
      // For example, mypackage/test/ is interpreted as if it 
      // were mypackage/test/**.
      $regex= preg_replace('#([/\\\\])$#', '$1**', $this->name);
      
      // Transform name element to regex filter
      $regex= str_replace('/', preg_quote($this->directorySeparator), $regex);
      $regex= str_replace('.', '\\.', $regex);

      // Transform single * to [^/]* (may match anything but another directory)
      $regex= preg_replace('#([^*])\\*([^*]|$)#', '$1[^'.preg_quote(preg_quote($this->directorySeparator)).']*$2', $regex);
      
      // Transform ** to .* (may match anything, any directory depth)
      $regex= str_replace('**', '.*', $regex);

      // Add delimiter and escape delimiter if already contained
      $regex= '#^'.str_replace('#', '\\#', $regex).'$#';
      
      return $regex;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function toFilter($base) {
      return new TopURIMatchesFilter($this->nameToRegex(), $base);
    }
  }
?>
