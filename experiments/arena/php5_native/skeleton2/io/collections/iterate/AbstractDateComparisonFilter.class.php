<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractDateComparisonFilter.class.php 8185 2006-10-16 10:24:01Z friebe $
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
     * @access  public
     * @param   &util.Date date
     */
    public function __construct(&$date) {
      $this->date= &$date;
    }
    
    /**
     * Accepts an element
     *
     * @model  abstract
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
      return $this->getClassName().'('.$this->date->toString().')';
    }
  
  } 
?>
