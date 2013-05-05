<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.mysqlx.AbstractMysqlxResultSet');

  /**
   * Result set
   *
   * @test  xp://net.xp_framework.unittest.rdbms.mysql.MySqlxBufferedResultSetTest
   */
  class MySqlxBufferedResultSet extends AbstractMysqlxResultSet {
    protected $records= array();
    protected $offset= 0;
    protected $length= 0;

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
      $this->length= sizeof($this->records);
    }
      
    /**
     * Seek
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) { 
      if ($offset < 0 || $offset >= $this->length) {
        throw new SQLException('Cannot seek to offset '.$offset.', out of bounds');
      }
      $this->offset= $offset;
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
      if ($this->offset >= $this->length) return FALSE;
      
      return $this->record($this->records[$this->offset++], $field);
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
