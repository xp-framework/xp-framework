<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'rdbms.criterion.CountProjection',
    'rdbms.criterion.ProjectionList',
    'rdbms.criterion.SimpleProjection'
  );

  /**
   * belongs to the Criterion API
   * projection factory
   *
   * @test     xp://net.xp_framework.unittest.rdbms.ProjectionTest
   * @see      xp://rdbms.Criteria
   * @purpose  purpose
   */
  class Projections extends Object {

    /**
     * manufactor a new ProjectionList
     *
     * @param   string[] properties
     * @return  rdbms.criterion.ProjectionList
     */
    public static function projectionList($properties= array()) {
      $pl= new ProjectionList();
      foreach ($properties as $property) $pl->add(new SimpleProjection($property, Projection::PROP));
      return $pl;
    }
    
    /**
     * manufactor a new CountProjection
     *
     * @param  string fieldname optional default is *
     * @param  string alias optional
     * @return  rdbms.criterion.CountProjection
     */
    public static function count($field= '*', $alias= '') {
      return new CountProjection($field, $alias);
    }
    
    /**
     * manufactor a new PropertyProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.PropertyProjection
     */
    public static function property($field, $alias= '') {
      return new SimpleProjection($field, Projection::PROP, $alias);
    }
    
    /**
     * manufactor a new AverageProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.AverageProjection
     */
    public static function average($field, $alias= '') {
      return new SimpleProjection($field, Projection::AVG, $alias);
    }
    
    /**
     * manufactor a new SumProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.SumProjection
     */
    public static function sum($field, $alias= '') {
      return new SimpleProjection($field, Projection::SUM, $alias);
    }
    
    /**
     * manufactor a new MaxProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.MaxProjection
     */
    public static function max($field, $alias= '') {
      return new SimpleProjection($field, Projection::MAX, $alias);
    }
    
    /**
     * manufactor a new MinProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.MinProjection
     */
    public static function min($field, $alias= '') {
      return new SimpleProjection($field, Projection::MIN, $alias);
    }
    
  }
?>
