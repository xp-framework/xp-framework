<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.CountProjection');
 uses('rdbms.criterion.AverageProjection');
 uses('rdbms.criterion.PropertyProjection');
 uses('rdbms.criterion.MinProjection');
 uses('rdbms.criterion.MaxProjection');
 uses('rdbms.criterion.SumProjection');
 uses('rdbms.criterion.ProjectionList');

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
     * manufactor a new PropertyProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.PropertyProjection
     */
    public static function property($field) {
      return new PropertyProjection($field);
    }
    
    /**
     * manufactor a new CountProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.CountProjection
     */
    public static function count($field) {
      return new CountProjection($field);
    }
    
    /**
     * manufactor a new AverageProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.AverageProjection
     */
    public static function average($field) {
      return new AverageProjection($field);
    }
    
    /**
     * manufactor a new SumProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.SumProjection
     */
    public static function sum($field) {
      return new SumProjection($field);
    }
    
    /**
     * manufactor a new MaxProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.MaxProjection
     */
    public static function max($field) {
      return new MaxProjection($field);
    }
    
    /**
     * manufactor a new MinProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.MinProjection
     */
    public static function min($field) {
      return new MinProjection($field);
    }
    
  }
?>
