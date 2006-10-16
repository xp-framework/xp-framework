<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Combined filter
   *
   * @purpose  Iteration Filter
   */
  class AbstractCombinedFilter extends Object {
    var
      $list  = array();

    var
      $_size = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   io.collections.iterate.IterationFilter[] list
     */
    function __construct($list) {
      $this->list= $list;
      $this->_size= sizeof($list);
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
      $s= $this->getClassName().'('.$this->_size.")@{\n";
      for ($i= 0; $i < $this->_size; $i++) {
        $s.= '  '.str_replace("\n", "\n  ", $this->list[$i]->toString())."\n";
      }
      return $s.'}';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
