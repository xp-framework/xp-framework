<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @ext      sybase_ct
   * @purpose  Resultset wrapper
   */
  class SybaseResultSet extends ResultSet {

    /**
     * Seek
     *
     * @access  public
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    function seek($offset) { 
      if (!sybase_data_seek($this->handle, $offset)) {
        return throw(new SQLException('Cannot seek to offset '.$offset));
      }
      return TRUE;
    }
    
    /**
     * Fetch a row (iterator function)
     *
     * @access  public
     * @return  array rowset or FALSE to indicate all rows have been fetched
     */
    function next() {
      if (FALSE === ($row= sybase_fetch_assoc($this->handle))) {
        return FALSE;
      }
      
      foreach (array_keys($row) as $key) {
        if ('datetime' == $this->fields[$key]) {
          $row[$key]= &Date::fromString($row[$key]);
        }
      }
      
      return $row;
    }
    
    /**
     * Close resultset and free result memory
     *
     * @access  public
     * @return  bool success
     */
    function close() { 
      return sybase_free_result($this->handle);
    }

  }
?>
