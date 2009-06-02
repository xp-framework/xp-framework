<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.XPIterator');

  /**
   * Iterates over a collection of classes, parsing them as going along.
   *
   * @test     xp://net.xp_framework.unittest.text.doclet.ClassIteratorTest
   * @purpose  Iterator
   */
  class ClassIterator extends Object implements XPIterator {
    public
      $classes = array(),
      $root    = NULL;
    
    protected 
      $offset  = NULL;
    
    /**
     * Constructor
     *
     * @param   string[] classes
     * @param   text.doclet.RootDoc root
     */
    public function __construct($classes= array(), RootDoc $root= NULL) {
      $this->classes= $classes;
      $this->offset= 0;
      $this->root= $root;
    }
    
    /**
     * Rewinds this iterator
     *
     */
    public function rewind() {
      $this->offset= 0;
    }

    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @return  bool
     */
    public function hasNext() {
      return $this->offset < sizeof($this->classes);
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  text.doclet.ClassDoc
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      if ($this->offset >= sizeof($this->classes)) {
        throw new NoSuchElementException('No more elements');
      }
      return $this->root->classNamed($this->classes[$this->offset++]);
    }
  } 
?>
