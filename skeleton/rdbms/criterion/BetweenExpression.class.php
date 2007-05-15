<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.criterion.Criterion');

  /**
   * Between expression
   *
   * @purpose  Criterion
   */
  class BetweenExpression extends Object implements Criterion {
    public
      $field  = '',
      $lo     = NULL,
      $hi     = NULL;

    /**
     * Constructor
     *
     * @param   string field
     * @param   mixed lo
     * @param   mixed hi
     */
    public function __construct($field, $lo, $hi) {
      $this->field= $field;
      $this->lo= $lo;
      $this->hi= $hi;
    }
  
    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types) { 
      if ($this->field instanceof Column) {
        $field= $this->field->asSQL($conn);
        $type=  $this->field->getType();
      } else {
        if (!isset($types[$this->field])) throw(new SQLStateException('Field "'.$this->field.'" unknown'));
        $field= $this->field;
        $type=  $types[$this->field][0];
      }

      return $field.' between '.$conn->prepare($type.' and '.$type, $this->lo, $this->hi);
    }
  } 
?>
