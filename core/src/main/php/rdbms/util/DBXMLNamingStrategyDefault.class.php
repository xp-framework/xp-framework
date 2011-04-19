<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

uses('rdbms.util.DBXMLNamingStrategy');

  /**
   * Generate Names for database generated classes
   *
   */
  class DBXMLNamingStrategyDefault extends DBXMLNamingStrategy {
    
    /**
     * assemble th name of a foreign key constraint
     *
     * @param   rdbms.DBTable t referencing table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    public function foreignKeyConstraintName($t, $c) {
      $role= '';
      foreach (array_keys($c->getKeys()) as $attribute) $role.= ucfirst('_id' == substr($attribute, -3) ? substr($attribute, 0, -3) : $attribute);
      return $role;
    }

    /**
     * assemble the name of a referencing foreign Key constraint
     * (current entity at the tip)
     *
     * @param   rdbms.DBTable t referencing table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    public function referencingForeignKeyConstraintName($t, $c) {
      $role= self::foreignKeyConstraintName($t, $c);
      return trim(((ucfirst($t->name) == $role) ? $role : ucfirst($t->name).$role));
    }

  }
?>
