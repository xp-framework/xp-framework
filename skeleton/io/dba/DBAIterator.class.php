<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Iterator over the keys of a DBA object
   *
   * Usage code snippet:
   * <code>
   *   // ...
   *   for ($i= &$db->iterator(); $i->hasNext(); ) {
   *     $key= $i->next();
   *     var_dump($key, $db->fetch($key));
   *   }
   *   // ...
   * </code>
   *
   * @see      xp://io.dba.DBAFile
   * @ext      dba
   * @purpose  Iterator
   */
  class DBAIterator extends Object {
    var
      $_key     = NULL,
      $_fd      = NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   resource fd
     * @see     xp://io.dba.DBAFile#iterator
     */
    function __construct($fd) {
      parent::__construct();
      $this->_fd= $fd;
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
      if (NULL === $this->_key) {   // First call
        $this->_key= dba_firstkey($this->_fd);
      } else {                      // Subsequent calls
        $this->_key= dba_nextkey($this->_fd);
      }
      return is_string($this->_key);
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  &mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    function &next() {
      if (!is_string($this->_key)) {
        return throw(new NoSuchElementException('No more elements'));
      }
      return $this->_key;
    }
  } implements(__FILE__, 'util.Iterator');
?>
