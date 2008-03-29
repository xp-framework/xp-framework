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
   * With the projection API a result set of an SQL query can be cut down to a
   * subset of result rows or aggregated (like sum, max, min, avg, count).
   * The projection represents the select part of a query (select <<projection>> from ...)
   * 
   * By default criteria projects the result to all rows of a table. This can be changed by
   * using the <tt>rdbms.Criteria::setProjection()</tt> method.
   *
   * Example
   * =======
   * <code>
   *   // cut the result down to 2 atributes of a result set
   *   // for further examples with ProjectionList see rdbms.criterion.ProjectionList API doc
   *   // sql: select name, surname from person;
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(
   *     Projections::projectionList()
   *     ->add(Projections::property(Person::column('name')))
   *     ->add(Projections::property(Person::column('surname')))
   *   ));
   *   
   *   // just count a result
   *   // for further examples with ProjectionList see rdbms.criterion.CountProjection API doc
   *   // sql: select count(*) from person where ...
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(Projections::count('*'))->add(...)...);
   *   
   *   // aggregated result set
   *   // sql: select avg(age) from person
   *   // sql: select min(age) from person
   *   // sql: select max(age) from person
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(Projections::avg(Person::column('age'))));
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(Projections::min(Person::column('age'))));
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(Projections::max(Person::column('age'))));
   *   
   *   // ProjectionList::add and Criteria::setprojection, can handle a second parameter as an ailias
   *   // sql: select max(age) as `oldest` from person
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(Projections::max(Person::column('age')), 'oldest'));
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.rdbms.ProjectionTest
   * @see      xp://rdbms.Criteria
   * @see      xp://rdbms.criterion.ProjectionList
   * @see      xp://rdbms.criterion.CountProjection
   * @purpose  Projection factory
   */
  class Projections extends Object {

    /**
     * manufactor a new ProjectionList
     *
     * @param   array<rdbms.SQLRenderable> properties
     * @return  rdbms.criterion.ProjectionList
     */
    public static function projectionList($properties= array()) {
      $pl= new ProjectionList();
      foreach ($properties as $property) $pl->add($property);
      return $pl;
    }
    
    /**
     * manufactor a new CountProjection
     *
     * @param   string fieldname optional default is *
     * @return  rdbms.criterion.CountProjection
     */
    public static function count($field= '*') {
      return new CountProjection($field);
    }
    
    /**
     * manufactor a new PropertyProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.PropertyProjection
     */
    public static function property($field, $alias= '') {
      return new SimpleProjection($field, Projection::PROP, $alias);
    }
    
    /**
     * manufactor a new AverageProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.AverageProjection
     */
    public static function average($field) {
      return new SimpleProjection($field, Projection::AVG);
    }
    
    /**
     * manufactor a new SumProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.SumProjection
     */
    public static function sum($field) {
      return new SimpleProjection($field, Projection::SUM);
    }
    
    /**
     * manufactor a new MaxProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.MaxProjection
     */
    public static function max($field) {
      return new SimpleProjection($field, Projection::MAX);
    }
    
    /**
     * manufactor a new MinProjection
     *
     * @param  string fieldname
     * @return  rdbms.criterion.MinProjection
     */
    public static function min($field) {
      return new SimpleProjection($field, Projection::MIN);
    }
  }
?>
