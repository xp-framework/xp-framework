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
   *   $origin= &new FileCollection('/etc');
   *
   *   for ($i= &new IOCollectionIterator($origin); $i->hasNext(); ) {
   *     Console::writeLine('Element ', xp::stringOf($i->next()));
   *   }
   * </code>
   *
   * @purpose  Iterator
   */
  class IOCollectionIterator extends Object implements XPIterator {
    public
      $collections = array(),
      $recursive   = FALSE;
    
    public
      $_element    = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.collections.IOCollection collection
     * @param   bool recursive default FALSE whether to recurse into subdirectories
     */
    public function __construct(&$collection, $recursive= FALSE) {
      $this->collections= array(&$collection);
      $this->collections[0]->open();
      $this->recursive= $recursive;
    }

    /**
     * Whether to accept a specific element. Always returns TRUE in this
     * implementation - overwrite in subclasses...
     *
     * @access  protected
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function acceptElement(&$element) {
      return TRUE;
    }
    
    /**
     * Returns true if the iteration has more elements. (In other words,
     * returns true if next would return an element rather than throwing
     * an exception.)
     *
     * @access  public
     * @return  bool
     */
    public function hasNext() {
      if ($this->_element) return TRUE; // next() not yet invoked, previously found entry available

      do {
        // End of collection, pop off stack and continue if there are more, 
        // returning otherwise
        $this->_element= &$this->collections[0]->next();
        if (NULL === $this->_element) {
          $this->collections[0]->close();
          array_shift($this->collections);

          if (empty($this->collections)) return FALSE; else continue;
        }

        // Check whether to recurse into subcollections
        if ($this->recursive && is('io.collections.IOCollection', $this->_element)) {
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
     * @access  public
     * @return  &io.collections.IOElement
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function &next() {
      if (!$this->hasNext()) {
        throw(new NoSuchElementException('No more  entries'));
      }
      
      $next= $this->_element;
      $this->_element= NULL;
      return $next;
    }
    
    /**
     * Creates a string representation of this iterator
     *
     * @access  public
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
