<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.Projection');

 define('AVG',  'avg(%c)');
 define('SUM',  'sum(%c)');
 define('MIN',  'min(%c)');
 define('MAX',  'max(%c)');
 define('PROP', '%c');

  /**
   * belongs to the Criterion API
   *
   */
  class SimpleProjection extends Projection {
  
    protected
      $field= '',
      $command= '',
      $alias= '';

    /**
     * constructor
     *
     * @param  string fieldname
     * @param  string command form constlist
     * @param  string alias optional
     */
    public function __construct($field, $command, $alias= '') {
      $this->field= $field;
      $this->command= $command;
      $this->alias= $alias;
    }

    /**
     * return the projection part of an SQL statement
     *
     * @param   &rdbms.DBConnection db
     * @return  string
     */
    public function toSQL($db) {
      return $db->prepare($this->command.' as %c', $this->field, ((0 == strlen($this->alias)) ? $this->field : $this->alias));
    }
  }
?>
