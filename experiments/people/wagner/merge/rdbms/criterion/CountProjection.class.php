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
     * return the projection part of an SQL statement
     *
     * @param   &rdbms.DBConnection db
     * @return  string
     */
    public function toSQL($db) {
      $command= ('*' == $this->field)       ? 'count(*)' : $db->prepare('count(%c)', $this->field);
      $alias=   ('*' == $this->field)       ? 'count'    : $db->prepare('count_%c',  $this->field);
      $alias=   (0 == strlen($this->alias)) ? $alias     : $db->prepare('%c',        $this->alias);
      return $command.' as '.$alias;
    }
  }
?>
