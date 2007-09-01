<?php
/* This class is part of the XP framework
 *
 * $Id: BetweenExpression.class.php 10683 2007-06-29 11:27:10Z friebe $ 
 */

  namespace rdbms::criterion;

  uses('rdbms.criterion.Criterion');

  /**
   * Between expression
   *
   * @purpose  Criterion
   */
  class BetweenExpression extends lang::Object implements Criterion {
    public
      $lhs    = '',
      $lo     = NULL,
      $hi     = NULL;

    /**
     * Constructor
     *
     * @param   mixed lhs either a string or an SQLFragment
     * @param   mixed lo
     * @param   mixed hi
     */
    public function __construct($lhs, $lo, $hi) {
      $this->lhs= $lhs;
      $this->lo= $lo;
      $this->hi= $hi;
    }
  
    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(rdbms::DBConnection $conn,  $peer) { 
      $lhs= ($this->lhs instanceof rdbms::SQLFragment) ? $this->lhs : $peer->column($this->lhs);

      return $conn->prepare(
        '%c between '.$lhs->getType().' and '.$lhs->getType(), 
        $lhs,
        $this->lo,
        $this->hi
      );
    }
  } 
?>
