<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.rdbms.mysql';

  uses(
    'unittest.TestCase',
    'rdbms.mysql.MySQLDBAdapter'
  );

  /**
   * TestCase
   *
   * @see      xp://rdbms.mysql.MySQLDBAdapter
   * @purpose  Unittest
   */
  class net·xp_framework·unittest·rdbms·mysql·TableDescriptionTest extends TestCase {
  
    /**
     * Test an auto_increment field
     *
     */
    #[@test]
    public function autoIncrement() {
      $this->assertEquals(
        new DBTableAttribute('contract_id', DB_ATTRTYPE_INT, TRUE, FALSE, 8, 0, 0),
        MySQLDBAdapter::tableAttributeFrom(array(
          'Field'   => 'contract_id',
          'Type'    => 'int(8)',
          'Null'    => '',
          'Key'     => 'PRI',
          'Default' => NULL,
          'Extra'   => 'auto_increment'
        ))
      );
    }

    /**
     * Test a field with unsigned
     *
     */
    #[@test]
    public function unsignedInt() {
      $this->assertEquals(
        new DBTableAttribute('bz_id', DB_ATTRTYPE_INT, FALSE, FALSE, 6, 0, 0),
        MySQLDBAdapter::tableAttributeFrom(array(
          'Field'   => 'bz_id',
          'Type'    => 'int(6) unsigned',
          'Null'    => '',
          'Key'     => '',
          'Default' => 500,
          'Extra'   => ''
        ))
      );
    }
  }
?>
