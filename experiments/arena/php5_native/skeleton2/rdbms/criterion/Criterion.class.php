<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.SQLStateException');

  /**
   * Represents a query criterion to be used in a Criteria query
   *
   * @see      xp://rdbms.Criteria#add
   * @purpose  Interface
   */
  interface Criterion {
  
    /**
     * Returns the fragment SQL
     *
     * @access  public
     * @param   &rdbms.DBConnection conn
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(&$conn, $types);
  }
?>
