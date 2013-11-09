<?php namespace net\xp_framework\unittest\rdbms\mysql;

use rdbms\mysql\MySQLDBAdapter;

/**
 * TestCase
 *
 * @see    xp://rdbms.mysql.MySQLDBAdapter
 */
class TableDescriptionTest extends \unittest\TestCase {

  #[@test]
  public function auto_increment() {
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

  #[@test]
  public function unsigned_int() {
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
