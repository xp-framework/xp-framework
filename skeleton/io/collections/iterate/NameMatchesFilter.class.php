<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Name filter
   *
   * @see      php://preg_match
   * @purpose  Iteration Filter
   */
  class NameMatchesFilter extends Object {
    var
      $pattern= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string pattern a Perl-compatible regular expression
     */
    function __construct($pattern) {
      $this->pattern= $pattern;
    }
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return (bool)preg_match($this->pattern, basename($element->getURI()));
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->pattern.')';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
