<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.SimpleProjection');

  /**
   * belongs to the Criterion API
   *
   */
  class CountProjection extends SimpleProjection {
    
    /**
     * constructor
     *
     * @param  rdbms.SQLRenderable field optional default is *
     * @param  string command form constlist
     * @param  string alias optional
     * @throws lang.IllegalArgumentException
     */
    public function __construct($field= '*', $alias= '') {
      if (('*' != $field) && !($field instanceof SQLRenderable)) throw new IllegalArgumentException('Argument #1 must be of type SQLRenderable or string "*"');
      $this->field= $field;
      $this->alias= $alias;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     */
    public function asSql(DBConnection $conn) {
      $field= ($this->field instanceof SQLRenderable) ? $this->field->asSQL($conn) : '*';
      $alias= (0 != strlen($this->alias)) ?  $this->alias : (('*' == $field) ? 'count' : 'count_'.$field);
      return $conn->prepare('count('.$field.') as %s', $alias);
    }
  }
?>
