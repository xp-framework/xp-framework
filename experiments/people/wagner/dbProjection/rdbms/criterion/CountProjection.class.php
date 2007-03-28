<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.Projection');

  /**
   * belongs to the Criterion API
   *
   */
  class CountProjection extends Projection {
    
    public
      $field= '';

    /**
     * constructor
     *
     * @param  string fieldname
     */
    public function __construct($field= '*') {
      $this->field= $field;
    }

    /**
     * return the projection part of an SQL statement
     *
     * @param   &rdbms.DBConnection db
     * @return  string
     */
    public function toSQL($db) {
      if ('*' == $this->field) return $db->prepare('count(%c) as count', $this->field);
      return $db->prepare('count(%c) as count_%c', $this->field, $this->field);
    }
  }
?>
