<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @purpose  Resultset wrapper
   */
  class MySqlxBufferedResultSet extends ResultSet {
    protected $records= array();

    /**
     * Constructor
     *
     * @param   resource handle
     */
    public function __construct($result, $fields, TimeZone $tz= NULL) {
      parent::__construct($result, $fields, $tz);
      while (NULL !== ($record= $this->handle->fetch($this->fields))) {
        $this->records[]= $record;
      }
      reset($this->records);
    }
      
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
      if (FALSE === ($record= current($this->records))) return NULL;
      next($this->records);
      return $field ? $record[$field] : $record;
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
