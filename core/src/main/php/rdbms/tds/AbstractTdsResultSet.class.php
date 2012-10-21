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
  abstract class AbstractTdsResultSet extends ResultSet {
   
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
        $return[$info['name']] = $record[$i];
      }
      return NULL === $field ? $return : $return[$field];
    } 
  }
?>
