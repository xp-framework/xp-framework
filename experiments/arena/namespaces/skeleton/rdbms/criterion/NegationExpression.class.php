<?php
/* This class is part of the XP framework
 *
 * $Id: NegationExpression.class.php 10596 2007-06-11 15:14:20Z ruben $ 
 */

  namespace rdbms::criterion;

  uses('rdbms.criterion.Criterion');

  /**
   * Negates another criterion
   *
   * @purpose  Criterion
   */
  class NegationExpression extends lang::Object implements Criterion {
    public
      $criterion  = NULL;

    /**
     * Constructor
     *
     * @param   rdbms.criterion.Criterion criterion
     */
    public function __construct($criterion) {
      $this->criterion= $criterion;
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
      return $conn->prepare('not (%c)', $this->criterion->asSql($conn, $peer));
    }
  } 
?>
