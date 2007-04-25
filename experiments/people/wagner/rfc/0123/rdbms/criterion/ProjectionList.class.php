<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.Projection');

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
     * return the projection part of an SQL statement
     *
     * @param   &rdbms.DBConnection db
     * @return  string
     */
    public function toSQL($db) {
      $s= '';
      foreach ($this->projections as $p) $s.= ','.$p->toSQL($db);
      return substr($s, 1);
    }

  }
?>
