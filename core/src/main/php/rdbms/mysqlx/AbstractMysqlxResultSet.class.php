<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.ResultSet');

  /**
   * Abstract base class
   *
   */
  abstract class AbstractMysqlxResultSet extends ResultSet {
   
    /**
     * Returns a record
     *
     * @param   [:var] record
     * @param   string field
     * @return  [:var] record
     */
    protected function record($record, $field= NULL) {
      $return= array();
      foreach ($this->fields as $i => $info) {
        $type= $info['type'];
        $value= $record[$i];

        if (NULL === $value) {
          $return[$info['name']]= NULL;
          continue;
        }

        switch ($type) {
          case 10:    // DATE
          case 11:    // TIME
          case 12:    // DATETIME
          case 14:    // NEWDATETIME
          case 7:     // TIMESTAMP
            $return[$info['name']]= NULL === $value || '0000-00-00 00:00:00' === $value ? NULL : Date::fromString($value, $this->tz);
            break;
          
          case 8:     // LONGLONG
          case 3:     // LONG
          case 9:     // INT24
          case 2:     // SHORT
          case 1:     // TINY
          case 16:    // BIT
            if ($value <= LONG_MAX && $value >= LONG_MIN) {
              $return[$info['name']]= (int)$value;
            } else {
              $return[$info['name']]= (double)$value;
            }
            break;
            
          case 4:     // FLOAT
          case 5:     // DOUBLE
          case 0:     // DECIMAL
          case 246:   // NEWDECIMAL
            $return[$info['name']]= (double)$value;
            break;

          case 253:   // CHAR
            $return[$info['name']]= (string)$value;
            break;

          default:
            $return[$info['name']]= $value;
        }
      }
      return NULL === $field ? $return : $return[$field];
    } 
  }
?>
