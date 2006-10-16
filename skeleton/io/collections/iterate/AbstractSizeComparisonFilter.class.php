<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Size comparison filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractSizeComparisonFilter extends Object {
    var
      $size= 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   int size the size to compare to in bytes
     */
    function __construct($size) {
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
    function accept(&$element) { }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->size.')';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
