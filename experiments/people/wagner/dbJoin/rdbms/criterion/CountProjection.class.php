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
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types, $aliasTable= '') {
      $tablePrefix= ($aliasTable) ? $aliasTable.'.' : '';
      if (is('rdbms.SQLFunction', $this->field)) throw new SQLStateException('count can not handle SQLFunction');
      if (('*' != $this->field) && !isset($types[$this->field])) throw new SQLStateException('field '.$field.' does not exist');
      $command= ('*' == $this->field)       ? 'count(*)' : $conn->prepare('count(%c)', $tablePrefix.$this->field);
      $alias=   ('*' == $this->field)       ? 'count'    : $conn->prepare('count_%c',  $this->field);
      $alias=   (0 == strlen($this->alias)) ? $alias     : $conn->prepare('%c',        $this->alias);
      return $command.' as '.$alias;
    }
  }
?>
