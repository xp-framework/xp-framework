<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Regular expression iteration filter
   *
   * @deprecated  Use NameMatchesFilter instead
   * @see      php://preg_match
   * @purpose  Iteration Filter
   */
  class RegexFilter extends Object implements IterationFilter {
    public
      $pattern= '';
      
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
      return (bool)preg_match($this->pattern, $element->getURI());
    }
  
  } 
?>
