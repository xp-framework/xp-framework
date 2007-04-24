<?php
/* This class is part of the XP framework
 *
 * $Id: SimpleExpression.class.php 9297 2007-01-16 12:02:37Z friebe $ 
 */

  uses('rdbms.criterion.Criterion');

  /**
   * Simple expression
   *
   * @purpose  Criterion
   */
  class FreeExpression extends Object implements Criterion {
    public
      $phrase= '';

    /**
     * Constructor
     *
     * @param   string phrase
     */
    public function __construct($phrase) {
      $this->phrase= $phrase;
    }
    
    /**
     * Creates a string representation of this expression.
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%s)',
        $this->getClassName(),
        $this->phrase
      );
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
      return '('.$this->phrase.')';      
    }

  } 
?>
