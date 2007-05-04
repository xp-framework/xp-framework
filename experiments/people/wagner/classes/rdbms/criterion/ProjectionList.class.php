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
     *
     * @param    rdbms.criterion.Projection projections
     * @return   rdbms.criterion.ProjectionList
     */
    public function add(Projection $projection) {
      $this->projections[]= $projection;
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
