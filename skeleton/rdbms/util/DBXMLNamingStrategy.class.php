<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generate Names for database generated classes
   *
   */
  abstract class DBXMLNamingStrategy extends Object {
    
    /**
     * assemble th name of a foreign key constraint
     *
     * @param   rdbms.DBTable t referenced table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    abstract function foreignKeyConstraintName($t, $c);

    /**
     * assemble the name of a referencing foreign Key constraint
     * (current entity at the tip)
     *
     * @param   rdbms.DBTable t referencing table
     * @param   rdbms.DBConstraint c
     * @return  string
     */
    abstract function referencingForeignKeyConstraintName($t, $c);
  }
?>
