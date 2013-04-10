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
      reset($this->records);
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
      if (FALSE === ($record= current($this->records))) return FALSE;
      
      next($this->records);
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
