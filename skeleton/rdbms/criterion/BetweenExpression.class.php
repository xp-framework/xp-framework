<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.criterion.Criterion');

  /**
   * Between expression
   *
   * @purpose  Criterion
   */
  class BetweenExpression extends Object implements Criterion {
    public
      $field  = '',
      $lo     = NULL,
      $hi     = NULL;

    /**
     * Constructor
     *
     * @param   string field
     * @param   mixed lo
     * @param   mixed hi
     */
    public function __construct($field, $lo, $hi) {
      $this->field= $field;
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
    public function asSql(DBConnection $conn, Peer $peer) { 
      $col= ($this->field instanceof Column) ? $this->field : $peer->column($this->field);
      return $col->asSQL($conn).' between '.$conn->prepare($col->getType().' and '.$col->getType(), $this->lo, $this->hi);
    }
  } 
?>
