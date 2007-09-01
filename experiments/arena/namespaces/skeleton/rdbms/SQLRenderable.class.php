<?php
/* This class is part of the XP framework
 *
 * $Id: SQLRenderable.class.php 10680 2007-06-29 11:14:38Z friebe $
 */

  namespace rdbms;

  uses('rdbms.SQLStateException');

  /**
   * Represents a fragment that can be rendered to string. Base interface
   * for SQLFragment and Projection interfaces.
   *
   * @see      xp://rdbms.SQLFragment
   * @see      xp://rdbms.criterion.Projection
   * @purpose  Interface
   */
  interface SQLRenderable {
  
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
