<?php
/* This class is part of the XP framework
 *
 * $Id: SQLExpression.class.php 10596 2007-06-11 15:14:20Z ruben $ 
 */

  namespace rdbms;

  /**
   * An SQL expression. Implemented by Criteria and Statement.
   *
   * @purpose  Interface
   */
  interface SQLExpression {
    
    /**
     * test if the expression is a projection
     *
     * @return  bool
     */
    public function isProjection();

    /**
     * test if the expression is a join
     *
     * @return  bool
     */
    public function isJoin();

    /**
     * Executes an SQL SELECT statement
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @return  rdbms.ResultSet
     */
    public function executeSelect(DBConnection $conn, Peer $peer);
  }
?>
