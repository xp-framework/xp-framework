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
  class PropertyProjection extends Projection {
    
    public
      $field= '';

    /**
     * constructor
     *
     * @param  string fieldname
     */
    public function __construct($field) {
      $this->field= $field;
    }

    /**
     * return the projection part of an SQL statement
     *
     * @param   &rdbms.DBConnection db
     * @return  string
     */
    public function toSQL($db) {
      return $db->prepare('%c', $this->field);
    }
  }
?>
