<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Date comparison iteration filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractDateComparisonFilter extends Object {
    var
      $date= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.Date date
     */
    function __construct(&$date) {
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
    function accept(&$element) { }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->date->toString().')';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
