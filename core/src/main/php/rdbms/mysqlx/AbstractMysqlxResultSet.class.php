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
        if (3 === $type) {
          $return[$info['name']]= (int)$value;
        } else if (246 === $type) {
          $return[$info['name']]= (double)$value;
        } else if (12 === $type || 7 === $type) {
          $return[$info['name']]= NULL === $value || '0000-00-00 00:00:00' === $value ? NULL : Date::fromString($value, $this->tz);
        } else {
          $return[$info['name']]= $value;
        }
      }
      return $return;    
    } 
  }
?>
