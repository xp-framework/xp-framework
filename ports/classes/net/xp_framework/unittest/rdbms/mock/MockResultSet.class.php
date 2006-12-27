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
    public
      $offset= 0;

    /**
     * Constructor
     *
     * @param   array<string, mixed>[] data
     */
    public function __construct($data= array()) {
      parent::__construct($data, NULL);
    }

    /**
     * Seek
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) {
      if ($offset < 0 || $offset >= sizeof($this->handle)) {
        throw(new SQLException('Seek to position #'.$offset.' failed'));
      }
      $this->offset= $offset;
      return TRUE;
    }
    
    /**
     * Iterator function. Returns a rowset if called without parameter,
     * the fields contents if a field is specified or FALSE to indicate
     * no more rows are available.
     *
     * @param   string field default NULL
     * @return  mixed
     */
    public function next($field= NULL) {
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
     * @return  bool success
     */
    public function close() {
      return TRUE;
    }  
  }
?>
