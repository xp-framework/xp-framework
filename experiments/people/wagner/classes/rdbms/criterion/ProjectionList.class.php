<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses(
   'rdbms.criterion.Projection',
   'rdbms.criterion.SimpleProjection'
 );

  /**
   * belongs to the Criterion API
   *
   */
  class ProjectionList extends Object implements Projection {

    protected
      $projections= array();

    /**
     * add projection
     * param can also be a rdbms.Column, a property
     * projection is then assumed
     *
     * @param    rdbms.criterion.Projection projections
     * @param  string alias optional
     * @return   rdbms.criterion.ProjectionList
     */
    public function add($projection, $alias= '') {
      $this->projections[]= ($projection instanceof SQLFragment)
      ? $projection= Projections::property($projection, $alias)
      : $projection;
      return $this;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn) {
      $s= '';
      foreach ($this->projections as $p) $s.= ', '.$p->asSql($conn);
      return substr($s, 1);
    }

  }
?>
