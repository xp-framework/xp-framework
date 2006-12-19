<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Name filter
   *
   * @see      php://preg_match
   * @purpose  Iteration Filter
   */
  class NameMatchesFilter extends Object implements IterationFilter {
    public
      $pattern= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string pattern a Perl-compatible regular expression
     */
    public function __construct($pattern) {
      $this->pattern= $pattern;
    }
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept(&$element) {
      return (bool)preg_match($this->pattern, basename($element->getURI()));
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->pattern.')';
    }
  
  } 
?>
