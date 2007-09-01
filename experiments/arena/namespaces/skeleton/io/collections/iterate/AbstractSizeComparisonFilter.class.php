<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractSizeComparisonFilter.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace io::collections::iterate;

  ::uses('io.collections.iterate.IterationFilter');

  /**
   * Size comparison filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractSizeComparisonFilter extends lang::Object implements IterationFilter {
    public
      $size= 0;
      
    /**
     * Constructor
     *
     * @param   int size the size to compare to in bytes
     */
    public function __construct($size) {
      $this->size= $size;
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
      return $this->getClassName().'('.$this->size.')';
    }
  
  } 
?>
