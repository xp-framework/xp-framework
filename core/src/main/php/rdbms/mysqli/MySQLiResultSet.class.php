<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @ext      mysqli
   * @purpose  Resultset wrapper
   */
  class MySQLiResultSet extends ResultSet {
  
    /**
     * Constructor
     *
     * @param   resource handle
     */
    public function __construct($result, TimeZone $tz= NULL) {
      $fields= array();
      if (is_object($result)) {
        while ($field= $result->fetch_field()) {
          $fields[$field->name]= $field->type;
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
      if (!mysqli_data_seek($this->handle, $offset)) {
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
     * @return  var
     */
    public function next($field= NULL) {
      if (
        !is_object($this->handle) ||
        FALSE === ($row= mysqli_fetch_assoc($this->handle))
      ) {
        return FALSE;
      }
      
      foreach (array_keys($row) as $key) {
        if (NULL === $row[$key] || !isset($this->fields[$key])) continue;
        switch ($this->fields[$key]) {
          case MYSQLI_TYPE_DATETIME:
          case MYSQLI_TYPE_DATE:
          case MYSQLI_TYPE_TIMESTAMP:
          case MYSQLI_TYPE_NEWDATE:
            $row[$key]= '0000-00-00 00:00:00' === $row[$key] ? NULL : Date::fromString($row[$key], $this->tz);
            break;
            
          case MYSQLI_TYPE_LONGLONG:
          case MYSQLI_TYPE_LONG:
          case MYSQLI_TYPE_INT24:
          case MYSQLI_TYPE_SHORT:
          case MYSQLI_TYPE_TINY:
          case MYSQLI_TYPE_BIT:
            if ($row[$key] <= LONG_MAX && $row[$key] >= LONG_MIN) {
              settype($row[$key], 'integer');
            } else {
              settype($row[$key], 'double');
            }
            break;

          case MYSQLI_TYPE_FLOAT:
          case MYSQLI_TYPE_DOUBLE:
          case MYSQLI_TYPE_DECIMAL:
          case MYSQLI_TYPE_NEWDECIMAL:
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
      if (!$this->handle) return;
      $r= mysqli_free_result($this->handle);
      $this->handle= NULL;
      return $r;
    }
  }
?>
