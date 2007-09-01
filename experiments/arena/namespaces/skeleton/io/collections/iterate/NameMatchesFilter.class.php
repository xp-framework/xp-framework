<?php
/* This class is part of the XP framework
 *
 * $Id: NameMatchesFilter.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace io::collections::iterate;

  ::uses('io.collections.iterate.IterationFilter');

  /**
   * Name filter
   *
   * @see      php://preg_match
   * @purpose  Iteration Filter
   */
  class NameMatchesFilter extends lang::Object implements IterationFilter {
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
      return (bool)preg_match($this->pattern, basename($element->getURI()));
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
