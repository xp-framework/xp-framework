<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.criterion.Criterion');

  /**
   * Negates another criterion
   *
   * @purpose  Criterion
   */
  class NegationExpression extends Object implements Criterion {
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
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types) { 
      return $conn->prepare('not (%c)', $this->criterion->asSql($conn, $types));
    }
  } 
?>
