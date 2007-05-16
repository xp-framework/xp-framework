<?php
/* This class is part of the XP framework
 *
 * $Id: SQLExpression.class.php 8895 2006-12-19 11:54:21Z kiesel $ 
 */

  /**
   * An SQL expression. Implemented by Criteria and Statement.
   *
   * @purpose  Interface
   */
  interface SQLExpression {
    
    public function isProjection();
    public function isJoin();
    public function executeSelect(DBConnection $conn, Peer $peer);
  }
?>
