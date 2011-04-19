<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.IOCollection', 'util.XPIterator');

  /**
   * Iterates over elements of a collection.
   *
   * <code>
   *   uses(
   *     'io.collections.FileCollection',
   *     'io.collections.iterate.IOCollectionIterator'
   *   );
   *
   *   $origin= new FileCollection('/etc');
   *   for ($i= new IOCollectionIterator($origin); $i->hasNext(); ) {
   *     Console::writeLine('Element ', xp::stringOf($i->next()));
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.io.collections.IOCollectionIteratorTest
   * @see      xp://io.collections.iterate.FilteredIOCollectionIterator
   * @purpose  Iterator
   */
  class IOCollectionIterator extends Object implements XPIterator, IteratorAggregate {
    public
      $collections = array(),
      $recursive   = FALSE;
    
    protected
      $iterator    = NULL,
      $_element    = NULL;
    
    /**
     * Constructor
     *
     * @param   io.collections.IOCollection collection
     * @param   bool recursive default FALSE whether to recurse into subdirectories
     */
    public function __construct($collection, $recursive= FALSE) {
      $this->collections= array($collection);
      $this->collections[0]->open();
      $this->recursive= $recursive;
    }

    /**
     * Whether to accept a specific element. Always returns TRUE in this
     * implementation - overwrite in subclasses...
     *
     * @param   io.collections.IOElement element
     * @return  bool
     */
    protected function acceptElement($element) {
      return TRUE;
    }

    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      if (!$this->iterator) $this->iterator= newinstance('Iterator', array($this), '{
        private $i, $t, $r;
        public function __construct($r) { $this->r= $r; }
        public function current() { return $this->r->next(); }
        public function key() { return $this->i; }
        public function next() { $this->i++; }
        public function rewind() { /* NOOP */ }
        public function valid() { return $this->r->hasNext(); }
      }');
      return $this->iterator;
    }
    
    /**
     * Returns true if the iteration has more elements. (In other words,
     * returns true if next would return an element rather than throwing
     * an exception.)
     *
     * @return  bool
     */
    public function hasNext() {
      if ($this->_element) return TRUE; // next() not yet invoked, previously found entry available

      do {
        // End of collection, pop off stack and continue if there are more, 
        // returning otherwise
        $this->_element= $this->collections[0]->next();
        if (NULL === $this->_element) {
          $this->collections[0]->close();
          array_shift($this->collections);

          if (empty($this->collections)) return FALSE; else continue;
        }

        // Check whether to recurse into subcollections
        if ($this->recursive && $this->_element instanceof IOCollection) {
          array_unshift($this->collections, $this->_element);
          $this->collections[0]->open();
        }
        
        // Check to see if the element is accepted. In case it isn't, continue searching
        if ($this->acceptElement($this->_element)) return TRUE;
      } while ($this->collections);

      return FALSE;
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  io.collections.IOElement
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      if (!$this->hasNext()) {
        throw new NoSuchElementException('No more entries');
      }
      
      $next= $this->_element;
      $this->_element= NULL;
      return $next;
    }
    
    /**
     * Creates a string representation of this iterator
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s<%s%s>',
        $this->getClassName(),
        xp::stringOf($this->collections[0]),
        $this->recursive ? '(R)' : ''
      );
    }

  } 
?>
