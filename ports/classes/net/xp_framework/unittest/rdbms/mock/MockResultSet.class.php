<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @purpose  Mock Object
   */
  class MockResultSet extends ResultSet {
    var
      $offset= 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   array<string, mixed>[] data
     */
    function __construct($data= array()) {
      parent::__construct($data, NULL);
    }

    /**
     * Seek
     *
     * @access  public
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    function seek($offset) {
      if ($offset < 0 || $offset >= sizeof($this->handle)) {
        return throw(new SQLException('Seek to position #'.$offset.' failed'));
      }
      $this->offset= $offset;
      return TRUE;
    }
    
    /**
     * Iterator function. Returns a rowset if called without parameter,
     * the fields contents if a field is specified or FALSE to indicate
     * no more rows are available.
     *
     * @access  public
     * @param   string field default NULL
     * @return  mixed
     */
    function next($field= NULL) {
      if ($this->offset >= sizeof($this->handle)) return FALSE;
      $this->offset++;
      
      if ($field) {
        return $this->handle[$this->offset- 1][$field];
      }
      return $this->handle[$this->offset- 1];
    }
    
    /**
     * Close resultset and free result memory
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      return TRUE;
    }  
  }
?>
