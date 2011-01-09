<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.iterate.IterationFilter');

  /**
   * Abstract base class for combined filters
   *
   * @see   xp://io.collections.iterate.AnyOfFilter
   * @see   xp://io.collections.iterate.AllOfFilter
   */
  abstract class AbstractCombinedFilter extends Object implements IterationFilter {
    public $list;
    protected $_size;
      
    /**
     * Constructor
     *
     * @param   io.collections.iterate.IterationFilter[] list
     */
    public function __construct($list= array()) {
      $this->list= $list;
      $this->_size= sizeof($list);
    }
    
    /**
     * Adds a filter
     *
     * @param   io.collections.iterate.IterationFilter filter
     * @return  io.collections.iterate.IterationFilter the added filter
     */
    public function add(IterationFilter $filter) {
      $this->list[]= $filter;
      $this->_size++;
      return $filter;
    }
    
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
