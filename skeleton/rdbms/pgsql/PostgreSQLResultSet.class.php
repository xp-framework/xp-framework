<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @ext      pgsql
   * @purpose  Resultset wrapper
   */
  class PostgreSQLResultSet extends ResultSet {
  
    /**
     * Constructor
     *
     * @param   resource handle
     */
    public function __construct($result) {
      $fields= array();
      if (is_resource($result)) {
        for ($i= 0, $num= pg_num_fields($result); $i < $num; $i++) {
          $fields[pg_field_name($result, $i)]= pg_field_type($result, $i);
        }
      }
      parent::__construct($result, $fields);
    }

    /**
     * Seek
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) { 
      if (!pg_data_seek($this->handle, $offset)) {
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
        FALSE === ($row= pg_fetch_assoc($this->handle))
      ) {
        return FALSE;
      }
      
      foreach (array_keys($row) as $key) {
        switch ($this->fields[$key]) {
          case 'date':
          case 'time':
          case 'timestamp':
            $row[$key]= Date::fromString($row[$key]);
            break;

          case 'bool':
            settype($row[$key], 'bool'); 
            break;
            
          case 'int2':
          case 'int4':
          case 'int8':
            settype($row[$key], 'integer'); 
            break;
            
          case 'float4':
          case 'float8':
          case 'numeric':
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
      return pg_free_result($this->handle);
    }
  }
?>
