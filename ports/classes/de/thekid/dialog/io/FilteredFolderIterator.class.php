<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.Folder');

  /**
   * Iterates over elements of a directory, only returning those elements that
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
      $pattern   = '',
      $folders   = array(),
      $recursive = FALSE;
    
    var
      $_entry  = FALSE;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.Folder folder
     * @param   string pattern regular expression
     * @param   bool recursive default FALSE whether to recurse into subdirectories
     */
    function __construct(&$folder, $pattern, $recursive= FALSE) {
      $this->folders= array(&$folder);
      $this->pattern= $pattern;
      $this->recursive= $recursive;
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
        if (FALSE === ($this->_entry= $this->folders[0]->getEntry())) {
          array_shift($this->folders);
          if (empty($this->folders)) return FALSE;
          continue;
        }
        if ($this->recursive && is_dir($this->folders[0]->getURI().$this->_entry)) {
          array_unshift($this->folders, new Folder($this->folders[0]->getURI().$this->_entry));
        }
      } while (!preg_match($this->pattern, $this->_entry));

      return TRUE;
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  string the fully qualified name of the element
     * @throws  util.NoSuchElementException when there are no more elements
     */
    function next() {
      if (FALSE === $this->_entry) {
        return throw(new NoSuchElementException('No more '.$this->pattern.' entries'));
      }
      return $this->folders[0]->getURI().$this->_entry;
    }  

  } implements(__FILE__, 'util.Iterator');
?>
