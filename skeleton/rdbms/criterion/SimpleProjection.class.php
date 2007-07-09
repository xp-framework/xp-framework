<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
  class SimpleProjection extends Object implements Projection {
    protected
      $field= '',
      $command= '',
      $alias= '';

    /**
     * constructor
     *
     * @param  rdbms.SQLRenderable field
     * @param  string command from Projection::constlist
     * @param  string alias optional
     */
    public function __construct(SQLRenderable $field, $command, $alias= '') {
      $this->field= $field;
      $this->command= $command;
      $this->alias= $alias;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn) {
      return (0 == strlen($this->alias))
      ? $conn->prepare($this->command, $this->field)
      : $conn->prepare($this->command.' as %l', $this->field, $this->alias);
    }
  }
?>
