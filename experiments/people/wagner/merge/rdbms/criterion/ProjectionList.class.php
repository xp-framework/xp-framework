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
  class ProjectionList extends Projection {

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
     * @param   array types
     * @return  string
     */
    public function asSql($conn, $types, $aliasTable= '') {
      $s= '';
      foreach ($this->projections as $p) $s.= ','.$p->asSql($conn, $types, $aliasTable);
      return substr($s, 1);
    }

  }
?>
