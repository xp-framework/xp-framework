<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractSizeComparisonFilter.class.php 8185 2006-10-16 10:24:01Z friebe $
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Size comparison filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractSizeComparisonFilter extends Object implements IterationFilter {
    public
      $size= 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   int size the size to compare to in bytes
     */
    public function __construct($size) {
      $this->size= $size;
    }
  
    /**
     * Accepts an element
     *
     * @model   abstract
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function accept(&$element) { }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->size.')';
    }
  
  } 
?>
