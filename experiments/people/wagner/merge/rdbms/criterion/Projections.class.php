<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.CountProjection');
 uses('rdbms.criterion.ProjectionList');
 uses('rdbms.criterion.SimpleProjection');

  /**
   * belongs to the Criterion API
   * projection factory
   *
   */
  class Projections extends Object {

    /**
     * manufactor a new ProjectionList
     *
     * @return  rdbms.criterion.ProjectionList
     */
    public static function projectionList() {
      return new ProjectionList();
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
      return new SimpleProjection($field, PROP, $alias);
    }
    
    /**
     * manufactor a new AverageProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.AverageProjection
     */
    public static function average($field, $alias= '') {
      return new SimpleProjection($field, AVG, $alias);
    }
    
    /**
     * manufactor a new SumProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.SumProjection
     */
    public static function sum($field, $alias= '') {
      return new SimpleProjection($field, SUM, $alias);
    }
    
    /**
     * manufactor a new MaxProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.MaxProjection
     */
    public static function max($field, $alias= '') {
      return new SimpleProjection($field, MAX, $alias);
    }
    
    /**
     * manufactor a new MinProjection
     *
     * @param  string fieldname
     * @param  string alias optional
     * @return  rdbms.criterion.MinProjection
     */
    public static function min($field, $alias= '') {
      return new SimpleProjection($field, MIN, $alias);
    }
    
  }
?>
