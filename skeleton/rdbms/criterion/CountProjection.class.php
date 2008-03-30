<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'rdbms.criterion.SimpleProjection',
    'lang.IllegalArgumentException'
  );

  /**
   * belongs to the Criterion API
   * Should be built with the static factory rdbms.criterion.Projections
   *
   * <code>
   *   // count all rows of the table Person
   *   // sql: select count * from person;
   *   // the following lines are equal
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(
   *     Projections::count('*')
   *   ));
   *   
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(
   *     Projections::count()
   *   ));
   *   
   *   // count all rows, where column "name" not NULL
   *   // sql: select count(name) from person;
   *   Person::getPeer()->doSelect(create(new Criteria())->setProjection(
   *     Projections::count(Person::column('name'))
   *   ));
   * </code>
   *
   * @see     xp://rdbms.criterion.Projections
   * @purpose rdbms.criterion
   */
  class CountProjection extends SimpleProjection {
    
    /**
     * constructor
     *
     * @param  mixed string('*') or rdbms.SQLRenderable field optional default is string('*')
     * @throws lang.IllegalArgumentException
     */
    public function __construct($field= '*') {
      if (('*' != $field) && !($field instanceof SQLRenderable)) throw new IllegalArgumentException('Argument #1 must be of type SQLRenderable or string "*"');
      $this->field= $field;
    }

    /**
     * Returns the fragment SQL as string
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     */
    public function asSql(DBConnection $conn) {
      $field= ($this->field instanceof SQLRenderable) ? $this->field->asSQL($conn) : '*';
      return $conn->prepare('count('.$field.')');
    }
  }
?>
