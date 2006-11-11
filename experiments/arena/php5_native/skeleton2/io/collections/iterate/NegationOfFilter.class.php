<?php
/* This class is part of the XP framework
 *
 * $Id: NegationOfFilter.class.php 8185 2006-10-16 10:24:01Z friebe $
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Negation filter
   *
   * @purpose  Iteration Filter
   */
  class NegationOfFilter extends Object implements IterationFilter {
    public
      $filter= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   io.collections.iterate.IterationFilter filter
     */
    public function __construct(&$filter) {
      $this->filter= $filter;
    }
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept(&$element) {
      return !$this->filter->accept($element);
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->filter->toString().'>';
    }
  
  } 
?>
