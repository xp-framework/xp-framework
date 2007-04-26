<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
uses('rdbms.util.DBXMLNamingStrategyDefault');

  /**
   * Generate Names for database generated classes
   *
   */
  class DBXMLNamingContext extends Object {
  
    protected static $strategy= NULL;
    
    function __static() {
      self::setStrategy(new DBXMLNamingStrategyDefault());
    }
    
    /**
     * set strategy
     *
     * @param   rdbms.DBXMLNameingStrategy s
     */
    static function setStrategy(DBXMLNamingStrategy $s) {
      self::$strategy= $s;
    }

    /**
     * assemble th name of a foreign key constraint
     *
     * @param   rdbms.DBTable t referencing table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    static function foreignKeyConstraintName(DBTable $t, DBConstraint $c) {
      return self::$strategy->foreignKeyConstraintName($t, $c);
    }

    /**
     * assemble the name of a referencing foreign Key constraint
     * (current entity at the tip)
     *
     * @param   rdbms.DBTable t referencing table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    static function referencingForeignKeyConstraintName(DBTable $t, DBConstraint $c) {
      return self::$strategy->referencingForeignKeyConstraintName($t, $c);
    }
  }
?>
