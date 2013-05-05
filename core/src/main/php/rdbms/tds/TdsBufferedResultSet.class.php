<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.tds.AbstractTdsResultSet');

  /**
   * Result set
   *
   * @test xp://net.xp_framework.unittest.rdbms.tds.TdsBufferedResultSetTest
   */
  class TdsBufferedResultSet extends AbstractTdsResultSet {
    protected $records= array();
    protected $offset= 0;
    protected $length= 0;

    /**
     * Constructor
     *
     * @param   var result
     * @param   [:var] fields
     * @param   util.TimeZone tz
     */
    public function __construct($result, $fields, TimeZone $tz= NULL) {
      parent::__construct($result, $fields, $tz);
      do {
        try {
          if (NULL === ($record= $this->handle->fetch($this->fields))) break;
          $this->records[]= $record;
        } catch (ProtocolException $e) {
          $this->records[]= new SQLException('Failed reading rows', $e);
        }
      } while (1);
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
      
      $record= $this->records[$this->offset++];
      if ($record instanceof SQLException) {
        throw $record;
      } else {
        return $this->record($record, $field);
      }
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
