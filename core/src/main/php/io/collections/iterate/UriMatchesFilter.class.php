<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Iteration filter matching on the complete URI. Always use forward
   * slashes to match on directory components regardless of the platform
   * the XP Framework is running on!
   *
   * @see   xp://io.collections.iterate.NameMatchesFilter
   * @see   php://preg_match
   */
  class UriMatchesFilter extends Object implements IterationFilter {
    public $pattern= '';
      
    /**
     * Constructor
     *
     * @param   string pattern a Perl-compatible regular expression
     */
    public function __construct($pattern) {
      $this->pattern= $pattern;
    }
  
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      return (bool)preg_match($this->pattern, strtr($element->getURI(), DIRECTORY_SEPARATOR, '/'));
    }

    /**
     * Creates a string representation of this iterator
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->pattern.')';
    }
  
  } 
?>
