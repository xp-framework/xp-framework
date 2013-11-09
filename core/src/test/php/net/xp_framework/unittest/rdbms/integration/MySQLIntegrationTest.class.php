<?php namespace net\xp_framework\unittest\rdbms\integration;

/**
 * MySQL integration test
 *
 * @ext       mysql
 */
class MySQLIntegrationTest extends RdbmsIntegrationTest {

  /**
   * Retrieve dsn
   *
   * @return  string
   */
  public function _dsn() {
    return 'mysql';
  }

  /**
   * Create autoincrement table
   *
   * @param   string name
   */
  protected function createAutoIncrementTable($name) {
    $this->removeTable($name);
    $this->db()->query('create table %c (pk int primary key auto_increment, username varchar(30))', $name);
  }
  
  /**
   * Create transactions table
   *
   * @param   string name
   */
  protected function createTransactionsTable($name) {
    $this->removeTable($name);
    $this->db()->query('create table %c (pk int, username varchar(30)) Engine=InnoDB', $name);
  }

  #[@test, @ignore('Numeric not supported by MySQL')]
  public function selectNumericNull() {
    parent::selectNumericNull();
  }

  #[@test, @ignore('Numeric not supported by MySQL')]
  public function selectNumeric() {
    parent::selectNumeric();
  }

  #[@test, @ignore('Numeric not supported by MySQL')]
  public function selectNumericZero() {
    parent::selectNumericZero();
  }

  #[@test, @ignore('Numeric not supported by MySQL')]
  public function selectNegativeNumeric() {
    parent::selectNegativeNumeric();
  }

  #[@test, @ignore('NumericWithScale not supported by MySQL')]
  public function selectNumericWithScaleNull() {
    parent::selectNumericWithScaleNull();
  }

  #[@test, @ignore('NumericWithScale not supported by MySQL')]
  public function selectNumericWithScale() {
    parent::selectNumericWithScale();
  }

  #[@test, @ignore('NumericWithScale not supported by MySQL')]
  public function selectNumericWithScaleZero() {
    parent::selectNumericWithScaleZero();
  }

  #[@test, @ignore('NumericWithScale not supported by MySQL')]
  public function selectNegativeNumericWithScale() {
    parent::selectNegativeNumericWithScale();
  }

  #[@test, @ignore('Numeric not supported by MySQL')]
  public function select64BitLongMaxPlus1Numeric() {
    parent::select64BitLongMaxPlus1Numeric();
  }

  #[@test, @ignore('Numeric not supported by MySQL')]
  public function select64BitLongMinMinus1Numeric() {
    parent::select64BitLongMinMinus1Numeric();
  }

  #[@test, @ignore('Decimal not supported by MySQL')]
  public function selectDecimalNull() {
    parent::selectDecimalNull();
  }

  #[@test, @ignore('Decimal not supported by MySQL')]
  public function selectDecimal() {
    parent::selectDecimal();
  }

  #[@test, @ignore('Decimal not supported by MySQL')]
  public function selectDecimalZero() {
    parent::selectDecimalZero();
  }

  #[@test, @ignore('Decimal not supported by MySQL')]
  public function selectNegativeDecimal() {
    parent::selectNegativeDecimal();
  }

  #[@test, @ignore('DecimalWithScale not supported by MySQL')]
  public function selectDecimalWithScaleNull() {
    parent::selectDecimalWithScaleNull();
  }

  #[@test, @ignore('DecimalWithScale not supported by MySQL')]
  public function selectDecimalWithScale() {
    parent::selectDecimalWithScale();
  }

  #[@test, @ignore('DecimalWithScale not supported by MySQL')]
  public function selectDecimalWithScaleZero() {
    parent::selectDecimalWithScaleZero();
  }

  #[@test, @ignore('DecimalWithScale not supported by MySQL')]
  public function selectNegativeDecimalWithScale() {
    parent::selectNegativeDecimalWithScale();
  }

  #[@test, @ignore('Cast to float not supported by MySQL')]
  public function selectFloat() {
    parent::selectFloat();
  }

  #[@test, @ignore('Cast to float not supported by MySQL')]
  public function selectFloatOne() {
    parent::selectFloatOne();
  }

  #[@test, @ignore('Cast to float not supported by MySQL')]
  public function selectFloatZero() {
    parent::selectFloatZero();
  }

  #[@test, @ignore('Cast to float not supported by MySQL')]
  public function selectNegativeFloat() {
    parent::selectNegativeFloat();
  }

  #[@test, @ignore('Cast to real not supported by MySQL')]
  public function selectReal() {
    parent::selectReal();
  }

  #[@test, @ignore('Cast to real not supported by MySQL')]
  public function selectRealOne() {
    parent::selectRealOne();
  }

  #[@test, @ignore('Cast to real not supported by MySQL')]
  public function selectRealZero() {
    parent::selectRealZero();
  }

  #[@test, @ignore('Cast to real not supported by MySQL')]
  public function selectNegativeReal() {
    parent::selectNegativeReal();
  }

  #[@test, @ignore('Cast to varchar not supported by MySQL')]
  public function selectEmptyVarChar() {
    parent::selectEmptyVarChar();
  }

  #[@test, @ignore('Cast to varchar not supported by MySQL')]
  public function selectVarChar() {
    parent::selectVarChar();
  }

  #[@test, @ignore('Cast to varchar not supported by MySQL')]
  public function selectNullVarChar() {
    parent::selectNullVarChar();
  }

  #[@test, @ignore('Money not supported by MySQL')]
  public function selectMoney() {
    parent::selectMoney();
  }

  #[@test, @ignore('Money not supported by MySQL')]
  public function selectHugeMoney() {
    parent::selectHugeMoney();
  }

  #[@test, @ignore('Money not supported by MySQL')]
  public function selectMoneyOne() {
    parent::selectMoneyOne();
  }

  #[@test, @ignore('Money not supported by MySQL')]
  public function selectMoneyZero() {
    parent::selectMoneyZero();
  }

  #[@test, @ignore('Money not supported by MySQL')]
  public function selectNegativeMoney() {
    parent::selectNegativeMoney();
  }

  #[@test, @ignore('Cast to text not supported by MySQL')]
  public function selectEmptyText() {
    parent::selectEmptyText();
  }

  #[@test, @ignore('Cast to text not supported by MySQL')]
  public function selectText() {
    parent::selectText();
  }

  #[@test, @ignore('Cast to text not supported by MySQL')]
  public function selectUmlautText() {
    parent::selectUmlautText();
  }

  #[@test, @ignore('Cast to text not supported by MySQL')]
  public function selectNulltext() {
    parent::selectNulltext();
  }

  #[@test, @ignore('Cast to Image not supported by MySQL')]
  public function selectEmptyImage() {
    parent::selectEmptyImage();
  }

  #[@test, @ignore('Cast to Image not supported by MySQL')]
  public function selectImage() {
    parent::selectImage();
  }

  #[@test, @ignore('Cast to Image not supported by MySQL')]
  public function selectUmlautImage() {
    parent::selectUmlautImage();
  }

  #[@test, @ignore('Cast to Image not supported by MySQL')]
  public function selectNullImage() {
    parent::selectNullImage();
  }

  #[@test, @ignore('Cast to binary not supported by MySQL')]
  public function selectEmptyBinary() {
    parent::selectEmptyBinary();
  }

  #[@test, @ignore('Cast to binary not supported by MySQL')]
  public function selectBinary() {
    parent::selectBinary();
  }

  #[@test, @ignore('Cast to binary not supported by MySQL')]
  public function selectUmlautBinary() {
    parent::selectUmlautBinary();
  }

  #[@test, @ignore('Cast to binary not supported by MySQL')]
  public function selectNullBinary() {
    parent::selectNullBinary();
  }

  #[@test, @ignore('Cast to varbinary not supported by MySQL')]
  public function selectEmptyVarBinary() {
    parent::selectEmptyVarBinary();
  }

  #[@test, @ignore('Cast to varbinary not supported by MySQL')]
  public function selectVarBinary() {
    parent::selectVarBinary();
  }

  #[@test, @ignore('Cast to varbinary not supported by MySQL')]
  public function selectUmlautVarBinary() {
    parent::selectUmlautVarBinary();
  }

  #[@test, @ignore('Cast to varbinary not supported by MySQL')]
  public function selectNullVarBinary() {
    parent::selectNullVarBinary();
  }

  #[@test]
  public function selectEmptyChar() {
    $this->assertEquals('', $this->db()->query('select cast("" as char(4)) as value')->next('value'));
  }

  #[@test]
  public function selectCharWithPadding() {
    $this->assertEquals('t', $this->db()->query('select cast("t" as char(4)) as value')->next('value'));
  }

  #[@test, @ignore('No known way to test this in MySQL')]
  public function readingRowFailsWithQuery() {
    parent::readingRowFailsWithQuery();
  }

  #[@test, @ignore('No known way to test this in MySQL')]
  public function readingRowFailsWithOpen() {
    parent::readingRowFailsWithOpen();
  }

  #[@test]
  public function selectSignedInt() {
    $this->assertEquals(1, $this->db()->query('select cast(1 as signed integer) as value')->next('value'));
  }

  #[@test, @ignore('MySQL does not know unsigned bigints')]
  public function selectMaxUnsignedBigInt() {
    parent::selectMaxUnsignedBigInt();
  }

  #[@test, @ignore('Cast to tinyint not supported by MySQL')]
  public function selectTinyint() {
    parent::selectTinyint();
  }

  #[@test, @ignore('Cast to tinyint not supported by MySQL')]
  public function selectTinyintOne() {
    parent::selectTinyintOne();
  }

  #[@test, @ignore('Cast to tinyint not supported by MySQL')]
  public function selectTinyintZero() {
    parent::selectTinyintZero();
  }

  #[@test, @ignore('Cast to smallint not supported by MySQL')]
  public function selectSmallint() {
    parent::selectSmallint();
  }

  #[@test, @ignore('Cast to smallint not supported by MySQL')]
  public function selectSmallintOne() {
    parent::selectSmallintOne();
  }

  #[@test, @ignore('Cast to smallint not supported by MySQL')]
  public function selectSmallintZero() {
    parent::selectSmallintZero();
  }
}
