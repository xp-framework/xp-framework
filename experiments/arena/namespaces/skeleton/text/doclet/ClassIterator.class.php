<?php
/* This class is part of the XP framework
 *
 * $Id: ClassIterator.class.php 9203 2007-01-08 21:58:03Z friebe $
 */

  namespace text::doclet;
 
  uses('util.XPIterator');

  /**
   * Iterates over a collection of classes, parsing them as going along.
   *
   * @purpose  Iterator
   */
  class ClassIterator extends lang::Object implements util::XPIterator {
    public
      $classes = array(),
      $root    = NULL,
      $_cur    = NULL;
    
    /**
     * Constructor
     *
     * @param   string[] classes
     */
    public function __construct($classes= array()) {
      $this->classes= array_flip($classes);
    }
    
    /**
     * Rewinds this iterator
     *
     */
    public function rewind() {
      reset($this->classes);
    }

    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @return  bool
     */
    public function hasNext() {
      if (NULL !== $this->_cur) return TRUE;
      while (NULL !== ($name= key($this->classes))) {
        if (NULL !== ($this->_cur= $this->root->classNamed($name))) return TRUE;
        next($this->classes);
      }
      return FALSE;
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      if (NULL === $this->_cur) {
        throw new util::NoSuchElementException('No more elements');
      }
      $return= $this->_cur;
      next($this->classes);
      $this->_cur= NULL;
      return $return;
    }

  } 
?>
