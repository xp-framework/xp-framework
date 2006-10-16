<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Name filter
   *
   * @purpose  Iteration Filter
   */
  class NameEqualsFilter extends Object {
    var
      $compare= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string compare the filename to compare to
     */
    function __construct($compare) {
      $this->compare= $compare;
    }
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return $this->compare == basename($element->getURI());
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'("'.$this->compare.'")';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
