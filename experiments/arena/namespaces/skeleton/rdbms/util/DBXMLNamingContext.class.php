<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::util;
uses('rdbms.util.DBXMLNamingStrategyDefault');

  /**
   * Generate Names for database generated classes
   *
   */
  class DBXMLNamingContext extends lang::Object {
  
    private static $strategy= NULL;
    
    static function __static() {
      self::setStrategy(new DBXMLNamingStrategyDefault());
    }
    
    /**
     * set strategy
     *
     * @param   rdbms.DBXMLNamingStrategy s
     */
    static function setStrategy( $s) {
      self::$strategy= $s;
    }

    /**
     * assemble th name of a foreign key constraint
     *
     * @param   rdbms.DBTable t referencing table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    static function foreignKeyConstraintName(rdbms::DBTable $t, rdbms::DBConstraint $c) {
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
    static function referencingForeignKeyConstraintName(rdbms::DBTable $t, rdbms::DBConstraint $c) {
      return self::$strategy->referencingForeignKeyConstraintName($t, $c);
    }
  }
?>
