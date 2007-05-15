<?php
/* This class is part of the XP framework
 *
 * $Id: Criterion.class.php 9172 2007-01-08 11:43:06Z friebe $ 
 */

  uses('rdbms.SQLStateException');

  /**
   * Represents a fragment that can be rendered to string
   *
   * @see      xp://rdbms.Criteria#add
   * @purpose  Interface
   */
  interface SQLRenderable  {
  
    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn);
  }
?>
