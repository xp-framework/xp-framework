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
     * @param  string fieldname optional default is *
     * @param  string command form constlist
     * @param  string alias optional
     */
    public function __construct($field= '*', $alias= '') {
      $this->field= $field;
      $this->alias= $alias;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn) {
      if (('*' != $this->field) && !isset($peer->types[$this->field])) throw new SQLStateException('field '.$field.' does not exist');
      $command= ('*' == $this->field)       ? 'count(*)' : $conn->prepare('count(%c)', $this->field);
      $alias=   ('*' == $this->field)       ? 'count'    : $conn->prepare('count_%c',  $this->field);
      $alias=   $conn->prepare('%s', (0 == strlen($this->alias)) ? $alias : $this->alias);
      return $command.' as '.$alias;
    }
  }
?>
