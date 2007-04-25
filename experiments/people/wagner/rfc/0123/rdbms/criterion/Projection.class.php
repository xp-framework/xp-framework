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
     * return the projection part of an SQL statement
     *
     * @param   &rdbms.DBConnection db
     * @return  string
     */
    abstract public function toSQL($db);
  }
?>
