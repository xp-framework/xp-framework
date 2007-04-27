<?php
/* This class is part of the XP framework
 *
 * $Id: LogicalExpression.class.php 9172 2007-01-08 11:43:06Z friebe $ 
 */

  define('LOGICAL_AND', 'and');
  define('LOGICAL_OR',  'or');

  uses('rdbms.criterion.Criterion');

  /**
   * Logical expression
   *
   * @purpose  Criterion
   */
  class LogicalExpression extends Object implements Criterion {
    public
      $criterions = array(),
      $op         = '';

    /**
     * Constructor
     *
     * @param   rdbms.criterion.Criterion[] criterions
     * @param   string op one of the LOGICAL_* constants
     */
    public function __construct($criterions, $op) {
      $this->criterions= $criterions;
      $this->op= $op;
    }
  
    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   array types
     * @param   string tablealias
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types, $aliasTable='') { 
      $sql= '';
      for ($i= 0, $s= sizeof($this->criterions); $i < $s; $i++) {
        $sql.= $this->criterions[$i]->asSql($conn, $types, $aliasTable).' '.$this->op.' ';
      }
      return '('.substr($sql, 0, (-1 * strlen($this->op)) - 2).')';
    }

  } 
?>
