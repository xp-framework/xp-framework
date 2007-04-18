<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class TopURIMatchesFilter extends Object implements IterationFilter {
    public
      $pattern= '',
      $basedir= '';
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($pattern, $basedir) {
      $this->pattern= $pattern;
      $this->basedir= $basedir;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function accept($element) {
      if (substr($element->getURI(), 0, strlen($this->basedir)) != $this->basedir) {
        throw new IllegalStateException('Element from wrong base: '.$element->getURI().' vs. '.$this->basedir);
      }

      $topdir= substr($element->getURI(), strlen($this->basedir)+ 1);
      return (bool)preg_match($this->pattern, $topdir);
    }
  }
?>
