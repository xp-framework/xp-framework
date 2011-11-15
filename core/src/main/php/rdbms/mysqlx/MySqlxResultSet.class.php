<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.mysqlx.AbstractMysqlxResultSet');

  /**
   * Result set
   *
   * @purpose  Resultset wrapper
   */
  class MySQLxResultSet extends AbstractMySQLxResultSet {
  
    /**
     * Seek
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) { 
      throw new SQLException('Cannot seek to offset '.$offset);
    }
    
    /**
     * Iterator function. Returns a rowset if called without parameter,
     * the fields contents if a field is specified or FALSE to indicate
     * no more rows are available.
     *
     * @param   string field default NULL
     * @return  var
     */
    public function next($field= NULL) {
      if (NULL === $this->handle || NULL === ($record= $this->handle->fetch($this->fields))) {
        $this->handle= NULL;
        return FALSE;
      }
      
      return $this->record($record, $field);
    }
    
    /**
     * Close resultset and free result memory
     *
     * @return  bool success
     */
    public function close() { 
      $this->handle= NULL;
      return TRUE;
    }
  }
?>
