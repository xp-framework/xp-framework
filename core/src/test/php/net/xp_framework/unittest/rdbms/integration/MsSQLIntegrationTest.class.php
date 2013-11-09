<?php namespace net\xp_framework\unittest\rdbms\integration;

/**
 * MSSQL integration test
 *
 * @see   https://github.com/xp-framework/xp-framework/issues/278
 * @see   http://msdn.microsoft.com/en-us/library/ms187942.aspx
 * @ext   mssql
 */
class MsSQLIntegrationTest extends RdbmsIntegrationTest {

  /**
   * Before class method: set minimun server severity;
   * otherwise server messages end up on the error stack
   * and will let the test fail (no error policy).
   */
  #[@beforeClass]
  public static function setMinimumServerSeverity() {
    if (function_exists('mssql_min_message_severity')) {
      mssql_min_message_severity(12);
    }
  }    

  /**
   * Creates table name
   *
   * @return  string
   */
  protected function tableName() {
    return '#unittest';
  }

  /**
   * Retrieve dsn
   *
   * @return  string
   */
  public function _dsn() {
    return 'mssql';
  }
  
  /**
   * Create autoincrement table
   *
   * @param   string name
   */
  protected function createAutoIncrementTable($name) {
    $this->removeTable($name);
    $this->db()->query('create table %c (pk int identity, username varchar(30))', $name);
  }
  
  /**
   * Create transactions table
   *
   * @param   string name
   */
  protected function createTransactionsTable($name) {
    $this->removeTable($name);
    $this->db()->query('create table %c (pk int, username varchar(30))', $name);
  }

  #[@test]
  public function selectDate() {
    $cmp= new \util\Date('2009-08-14 12:45:00');
    $result= $this->db()->query('select convert(datetime, %s, 120) as value', $cmp)->next('value');

    $this->assertInstanceOf('util.Date', $result);
    $this->assertEquals($cmp->toString('Y-m-d'), $result->toString('Y-m-d'));
  }

  #[@test]
  public function selectNVarchar() {
    $this->assertEquals('Test', $this->db()->query('select cast("Test" as nvarchar) as value')->next('value'));
  }

  #[@test]
  public function selectNVarchars() {
    $this->assertEquals(
      array('one' => 'Test1', 'two' => 'Test2'),
      $this->db()->query('select cast("Test1" as nvarchar) as one, cast("Test2" as nvarchar) as two')->next()
    );
  }

  #[@test]
  public function selectVarcharVariant() {
    $this->assertEquals('Test', $this->db()->query('select cast("Test" as sql_variant) as value')->next('value'));
  }

  #[@test]
  public function selectVarcharVariants() {
    $this->assertEquals(
      array('one' => 'Test1', 'two' => 'Test2'), 
      $this->db()->query('select cast("Test1" as sql_variant) as one, cast("Test2" as sql_variant) as two')->next()
    );
  }

  #[@test]
  public function selectIntegerVariant() {
    $this->assertEquals(1, $this->db()->query('select cast(1 as sql_variant) as value')->next('value'));
  }

  #[@test]
  public function selectDecimalVariant() {
    $this->assertEquals(1.2, $this->db()->query('select cast(1.2 as sql_variant) as value')->next('value'));
  }

  #[@test]
  public function selectNumericVariantWithFollowingVarchar() {
    $this->assertEquals(
      array('n' => 10, 'v' => 'Test'),
      $this->db()->query('select cast(convert(numeric, 10) as sql_variant) as n, "Test" as v')->next()
    );
  }

  #[@test]
  public function selectMoneyVariant() {
    $this->assertEquals(1.23, $this->db()->query('select cast($1.23 as sql_variant) as value')->next('value'));
  }

  #[@test]
  public function selectDateVariant() {
    $cmp= new \util\Date('2009-08-14 12:45:00');
    $this->assertEquals($cmp, $this->db()->query('select cast(convert(datetime, %s, 102) as sql_variant) as value', $cmp)->next('value'));
  }

  #[@test]
  public function selectUniqueIdentifierariant() {
    $cmp= '0E984725-C51C-4BF4-9960-E1C80E27ABA0';
    $this->assertEquals($cmp, $this->db()->query('select cast(convert(uniqueidentifier, %s) as sql_variant) as value', $cmp)->next('value'));
  }

  #[@test]
  public function selectUniqueIdentifier() {
    $cmp= '0E984725-C51C-4BF4-9960-E1C80E27ABA0';
    $this->assertEquals($cmp, $this->db()->query('select convert(uniqueidentifier, %s) as value', $cmp)->next('value'));
  }

  #[@test]
  public function selectNullUniqueIdentifier() {
    $this->assertNull($this->db()->query('select convert(uniqueidentifier, NULL) as value')->next('value'));
  }

  #[@test, @ignore('MsSQL does not know unsigned ints')]
  public function selectUnsignedInt() {
    parent::selectUnsignedInt();
  }

  #[@test, @ignore('MsSQL does not know unsigned bigints')]
  public function selectMaxUnsignedBigInt() {
    parent::selectMaxUnsignedBigInt();
  }

  #[@test, @expect(class = 'rdbms.SQLStatementFailedException', withMessage= '/More power/')]
  public function raiseError() {
    $this->db()->query('raiserror 61000 "More power"');
  }

  #[@test]
  public function printMessage() {
    $q= $this->db()->query('
      print "More power"
      select 1 as "result"
    ');
    $this->assertEquals(1, $q->next('result'));
  }
}
