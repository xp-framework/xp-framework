<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Regular expression iteration filter
   *
   * @deprecated  Use NameMatchesFilter instead
   * @see      php://preg_match
   * @purpose  Iteration Filter
   */
  class RegexFilter extends Object {
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
      return (bool)preg_match($this->pattern, $element->getURI());
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
