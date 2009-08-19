<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @ext      sqlsrv
   * @purpose  Resultset wrapper
   */
  class SqlSrvResultSet extends ResultSet {
  
    /**
     * Constructor
     *
     * @param   resource handle
     */
    public function __construct($result, TimeZone $tz= NULL) {
      $fields= array();
      if (is_resource($result)) {
        foreach (sqlsrv_field_metadata($result) as $meta) {
          $fields[$meta['Name']]= $meta;
        }
      }
      parent::__construct($result, $fields, $tz);
    }

    /**
     * Seek
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) { 
      if (!sqlsrv_data_seek($this->handle, $offset)) {
        throw new SQLException('Cannot seek to offset '.$offset);
      }
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
      if (
        !is_resource($this->handle) ||
        !is_array($row= sqlsrv_fetch_array($this->handle, SQLSRV_FETCH_ASSOC))
      ) {
        return FALSE;
      }

      foreach ($row as $key => $value) {
        if (NULL === $value || !isset($this->fields[$key])) continue;
        
        if ($value instanceof DateTime) {
          $row[$key]= new Date($value);
        } else switch ($this->fields[$key]['Type']) {
          case -9: // SQLSRV_SQLTYPE_DATETIME, SQLSRV_SQLTYPE_SMALLDATETIME
            $row[$key]= new Date($row[$key]); 
            break;

          case 2:  // SQLSRV_SQLTYPE_NUMERIC
            if ($this->fields[$key]['Scale'] > 0) {
              settype($row[$key], 'double');
              break;
            }
            // Fall through intentionally
          
          case 4:  // SQLSRV_SQLTYPE_INT
            if ($value <= LONG_MAX && $value >= LONG_MIN) {
              settype($row[$key], 'integer');
            } else {
              settype($row[$key], 'double');
            }
            break;

          case 7: // SQLSRV_SQLTYPE_REAL
            settype($row[$key], 'double');
            break;
        }
      }
      
      if ($field) return $row[$field]; else return $row;
    }
    
    /**
     * Close resultset and free result memory
     *
     * @return  bool success
     */
    public function close() { 
      return sqlsrv_free_stmt($this->handle);
    }
  }
?>
