<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * belongs to the Criterion API
   *
   */
  abstract class Projection extends Object {

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   array types
     * @return  string
     */
    public function asSql($conn, $types) {}
  }
?>
