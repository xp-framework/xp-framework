<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Iterates over elements a directory, only returning those elements that
   * match the given pattern.
   *
   * <code>
   *   $origin= &new Folder('...');
   *
   *   for ($i= &new FilteredFolderIterator($origin, '/\.jpe?g$/i'); $i->hasNext(); ) {
   *     Console::writeLine('JPEG-Image ', $i->next());
   *   }
   * </code>
   *
   * @purpose  Iterator
   */
  class FilteredFolderIterator extends Object {
    var
      $pattern = '',
      $folder  = NULL;
    
    var
      $_entry  = FALSE;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.Folder folder
     * @param   string pattern regular expression
     */
    function __construct(&$folder, $pattern) {
      $this->folder= &$folder;
      $this->pattern= $pattern;
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
      do {
        if (FALSE === ($this->_entry= $this->folder->getEntry())) return FALSE;
      } while (!preg_match($this->pattern, $this->_entry));

      return TRUE;
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  string the non-fully qualified name of the element
     * @throws  util.NoSuchElementException when there are no more elements
     */
    function next() {
      if (FALSE === $this->_entry) {
        return throw(new NoSuchElementException('No more '.$this->pattern.' entries'));
      }
      return $this->_entry;
    }  

  } implements(__FILE__, 'util.Iterator');
?>
