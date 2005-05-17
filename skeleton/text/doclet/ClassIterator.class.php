<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Iterates over a collection of classes, parsing them as going along.
   *
   * @purpose  Iterator
   */
  class ClassIterator extends Object {
    var
      $classes = array(),
      $root    = NULL,
      $_key    = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string[] classes
     */
    function __construct($classes= array()) {
      $this->classes= array_flip($classes);
    }
    
    /**
     * Rewinds this iterator
     *
     * @access  public
     */
    function rewind() {
      reset($this->classes);
    }

    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @access  public
     * @return  bool
     */
    function hasNext() {
      return !is_null($this->_key= key($this->classes));
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  &mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    function &next() {
      if (is_null($this->_key)) {
        return throw(new NoSuchElementException('No more elements'));
      }
      next($this->classes);
      return $this->root->classNamed($this->_key);
    }

  } implements(__FILE__, 'util.Iterator');
?>
