<?php
/* This class is part of the XP framework
 *
 * $Id: Criterion.class.php 9172 2007-01-08 11:43:06Z friebe $ 
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
     * @param   rdbms.DBConnection conn
     * @param   array types
     * @param   string optional
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types, $aliasTable= '');
  }
?>
