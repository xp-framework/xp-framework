<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::criterion;

  uses('rdbms.criterion.Projection');

  /**
   * belongs to the Criterion projection API
   * simple base class 
   * Do not use, use factory rdbms.criterion.Projections instead
   *
   * @see     xp://rdbms.criterion.Projections
   * @see     xp://rdbms.criterion.CountProjection
   * @see     xp://rdbms.criterion.ProjectionList
   * @purpose rdbms.criterion
   */
  class SimpleProjection extends lang::Object implements Projection {
    protected
      $field= '',
      $command= '';

    /**
     * constructor
     *
     * @param  rdbms.SQLRenderable field
     * @param  string command from Projection::constlist
     * @param  string alias optional
     */
    public function __construct(rdbms::SQLRenderable $field, $command) {
      $this->field= $field;
      $this->command= $command;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(rdbms::DBConnection $conn) {
      return $conn->prepare($this->command, $this->field);
    }
  }
?>
