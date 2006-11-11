<?php
/* This class is part of the XP framework
 *
 * $Id: FilteredIOCollectionIterator.class.php 8186 2006-10-16 10:24:18Z friebe $
 */

  uses('io.collections.iterate.IOCollectionIterator');


  /**
   * Iterates over elements of a folder, only returning those elements that
   * are accepted by the specified filter.
   *
   * <code>
   *   uses(
   *     'io.collections.FileCollection',
   *     'io.collections.iterate.FilteredIOCollectionIterator',
   *     'io.collections.iterate.RegexFilter'
   *   );
   *
   *   $origin= &new FileCollection('/etc');
   *   for (
   *     $i= &new FilteredIOCollectionIterator($origin, new RegexFilter('/\.jpe?g$/i')); 
   *     $i->hasNext(); 
   *   ) {
   *     Console::writeLine('Element ', xp::stringOf($i->next()));
   *   }
   * </code>
   *
   * @see      xp://io.collections.iterate.IOCollectionIterator
   * @purpose  Iterator
   */
  class FilteredIOCollectionIterator extends IOCollectionIterator {
    public
      $filter    = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.collections.IOCollection collection
     * @param   &io.collections.iterate.Filter filter
     * @param   bool recursive default FALSE whether to recurse into subdirectories
     */
    public function __construct(&$collection, &$filter, $recursive= FALSE) {
      parent::__construct($collection, $recursive);
      $this->filter= &$filter;
    }
    
    /**
     * Whether to accept a specific element
     *
     * @access  protected
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    public function acceptElement(&$element) {
      return $this->filter->accept($element);
    }
    
    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString()."@{\n  ".str_replace("\n", "\n  ", $this->filter->toString())."\n}";
    }
  }
?>
