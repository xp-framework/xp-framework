<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Name filter
   *
   * @purpose  Iteration Filter
   */
  class NameEqualsFilter extends Object implements IterationFilter {
    public
      $compare= '';
      
    /**
     * Constructor
     *
     * @param   string compare the filename to compare to
     */
    public function __construct($compare) {
      $this->compare= $compare;
    }
  
    /**
     * Accepts an element
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    public function accept($element) {
      return $this->compare == basename($element->getURI());
    }

    /**
     * Creates a string representation of this iterator
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'("'.$this->compare.'")';
    }
  
  } 
?>
