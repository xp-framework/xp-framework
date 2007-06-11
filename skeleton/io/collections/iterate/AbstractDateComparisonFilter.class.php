<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Date comparison iteration filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractDateComparisonFilter extends Object implements IterationFilter {
    public
      $date= NULL;
      
    /**
     * Constructor
     *
     * @param   util.Date date
     */
    public function __construct($date) {
      $this->date= $date;
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
      return $this->getClassName().'('.$this->date->toString().')';
    }
  
  } 
?>
