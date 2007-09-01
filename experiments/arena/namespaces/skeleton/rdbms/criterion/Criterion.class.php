<?php
/* This class is part of the XP framework
 *
 * $Id: Criterion.class.php 10596 2007-06-11 15:14:20Z ruben $ 
 */

  namespace rdbms::criterion;

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
     * @param   rdbms.Peer peer
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(rdbms::DBConnection $conn,  $peer);
  }
?>
