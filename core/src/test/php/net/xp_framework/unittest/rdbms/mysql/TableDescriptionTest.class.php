<?php namespace net\xp_framework\unittest\rdbms\mysql;



use unittest\TestCase;
use rdbms\mysql\MySQLDBAdapter;


/**
 * TestCase
 *
 * @see      xp://rdbms.mysql.MySQLDBAdapter
 * @purpose  Unittest
 */
class TableDescriptionTest extends TestCase {

  /**
   * Test an auto_increment field
   *
   */
  #[@test]
  public function autoIncrement() {
    $this->assertEquals(
      new \rdbms\DBTableAttribute('contract_id', DB_ATTRTYPE_INT, true, false, 8, 0, 0),
      MySQLDBAdapter::tableAttributeFrom(array(
        'Field'   => 'contract_id',
        'Type'    => 'int(8)',
        'Null'    => '',
        'Key'     => 'PRI',
        'Default' => null,
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
      new \rdbms\DBTableAttribute('bz_id', DB_ATTRTYPE_INT, false, false, 6, 0, 0),
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
