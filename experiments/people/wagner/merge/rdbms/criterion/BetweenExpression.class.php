<?php
/* This class is part of the XP framework
 *
 * $Id: BetweenExpression.class.php 9287 2007-01-15 21:01:10Z friebe $ 
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
     * @param   string optional
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types, $aliasTable= '') { 
      $tablePrefix= ($aliasTable) ? $aliasTable.'.' : '';
      if (!is('rdbms.SQLFunction', $this->field) && !isset($types[$field])) throw new SQLStateException('field '.$field.' does not exist');
      $field= is('rdbms.SQLFunction', $this->field) ? $this->field->asSql($conn, $types, $aliasTable) : $aliasTable.$this->field;
      $lo=    is('rdbms.SQLFunction', $this->lo)    ? $this->lo->asSql($conn, $types, $aliasTable)    : $this->lo;
      $hi=    is('rdbms.SQLFunction', $this->hi)    ? $this->hi->asSql($conn, $types, $aliasTable)    : $this->hi;

      return $this->field.' between '.$conn->prepare(
        '%c and %c',
        $lo,
        $hi
      );
    }
  } 
?>
