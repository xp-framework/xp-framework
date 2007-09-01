<?php
/* This class is part of the XP framework
 *
 * $Id: RegexFilter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace io::collections::iterate;

  ::uses('io.collections.iterate.IterationFilter');

  /**
   * Regular expression iteration filter
   *
   * @deprecated  Use NameMatchesFilter instead
   * @see      php://preg_match
   * @purpose  Iteration Filter
   */
  class RegexFilter extends lang::Object implements IterationFilter {
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
