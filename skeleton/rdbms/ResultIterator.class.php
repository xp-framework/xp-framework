<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Iterator over a resultset
   *
   * @see      xp://rdbms.Peer
   * @purpose  Iterator
   */
  class ResultIterator extends Object {
    var
      $_rs         = NULL,
      $_identifier = '',
      $_record     = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.ResultSet rs
     * @param   string identifier
     * @see     xp://rdbms.Peer#iteratorFor
     */
    function __construct(&$rs, $identifier) {
      $this->_rs= &$rs;
      $this->_identifier= $identifier;
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
      $this->_record= &$this->_rs->next();
      return !empty($this->_record);
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  &rdbms.DataSet
     * @throws  util.NoSuchElementException when there are no more elements
     */
    function &next() {
      if (empty($this->_record)) {
        return throw(new NoSuchElementException('No more elements'));
      }
      
      return new $this->_identifier($this->_record);
    }
  } implements(__FILE__, 'util.Iterator');
?>
