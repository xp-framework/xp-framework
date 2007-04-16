<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.iterate.RegexFilter');

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
    public function toFilter() {
      // Transform name element to regex filter
      $regex= $this->name;
      $regex= preg_replace('#\*\*#', '.+', $regex);
      $regex= preg_replace('#([^*])\*#', '$1[^'.preg_quote(DIRECTORY_SEPARATOR).']+', $regex);

      if ('/' != DIRECTORY_SEPARATOR) {
        $regex= str_replace('/', preg_quote(DIRECTORY_SEPARATOR), $regex);
      }
      var_dump($this->name, $regex);
      
      return new RegexFilter($regex);
    }
  }
?>
