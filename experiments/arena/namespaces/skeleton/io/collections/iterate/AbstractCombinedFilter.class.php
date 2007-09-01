<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractCombinedFilter.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace io::collections::iterate;

  ::uses('io.collections.iterate.IterationFilter');

  /**
   * Combined filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractCombinedFilter extends lang::Object implements IterationFilter {
    public
      $list  = array();

    public
      $_size = 0;
      
    /**
     * Constructor
     *
     * @param   io.collections.iterate.IterationFilter[] list
     */
    public function __construct($list) {
      $this->list= $list;
      $this->_size= sizeof($list);
    }
    
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) { }

    /**
     * Creates a string representation of this iterator
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'('.$this->_size.")@{\n";
      for ($i= 0; $i < $this->_size; $i++) {
        $s.= '  '.str_replace("\n", "\n  ", $this->list[$i]->toString())."\n";
      }
      return $s.'}';
    }
  
  } 
?>
