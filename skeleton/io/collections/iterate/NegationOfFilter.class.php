<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Negation filter
   *
   * @purpose  Iteration Filter
   */
  class NegationOfFilter extends Object {
    var
      $filter= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   io.collections.iterate.IterationFilter filter
     */
    function __construct(&$filter) {
      $this->filter= $filter;
    }
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return !$this->filter->accept($element);
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'<'.$this->filter->toString().'>';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
