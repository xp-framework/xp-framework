<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Negates another criterion
   *
   * @purpose  Criterion
   */
  class NegationExpression extends Object {
    var
      $criterion  = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &rdbms.criterion.Criterion criterion
     */
    function __construct(&$criterion) {
      $this->criterion= &$criterion;
    }
  
    /**
     * Returns the fragment SQL
     *
     * @access  public
     * @param   &rdbms.DBConnection conn
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    function asSql(&$conn, $types) { 
      return $conn->prepare('not (%c)', $this->criterion->asSql($conn, $types));
    }

  } implements(__FILE__, 'rdbms.criterion.Criterion');
?>
