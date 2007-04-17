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
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($name= NULL) {
      $this->name= $name;
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
    public function toFilter($base) {

      // Transform name element to regex filter
      if ('/' != DIRECTORY_SEPARATOR) {
        $regex= str_replace('/', preg_quote(DIRECTORY_SEPARATOR), $this->name);
      }

      // Transform single * to [^/]* (may match anything but another directory)
      $regex= preg_replace('#([^*])\*#', '$1[^'.preg_quote(DIRECTORY_SEPARATOR).']*', $regex);
      
      
      // Transform ** to .* (may match anything, any directory depth)
      $regex= str_replace('**', '.*', $regex);

      // Add delimiter and escape delimiter if already contained
      $regex= '#^'.str_replace('#', '\#', $regex).'$#';
      
      return new TopURIMatchesFilter($regex, $base);
    }
  }
?>
