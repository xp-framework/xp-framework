<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

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

    /**
     * Test selecting numeric values
     *
     */
    #[@test, @ignore('Numeric not supported by MySQL')]
    public function selectNumericNull() {
      parent::selectNumericNull();
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test, @ignore('Numeric not supported by MySQL')]
    public function selectNumeric() {
      parent::selectNumeric();
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test, @ignore('Numeric not supported by MySQL')]
    public function selectNumericZero() {
      parent::selectNumericZero();
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test, @ignore('Numeric not supported by MySQL')]
    public function selectNegativeNumeric() {
      parent::selectNegativeNumeric();
    }

    /**
     * Test selecting NumericWithScale values
     *
     */
    #[@test, @ignore('NumericWithScale not supported by MySQL')]
    public function selectNumericWithScaleNull() {
      parent::selectNumericWithScaleNull();
    }

    /**
     * Test selecting NumericWithScale values
     *
     */
    #[@test, @ignore('NumericWithScale not supported by MySQL')]
    public function selectNumericWithScale() {
      parent::selectNumericWithScale();
    }

    /**
     * Test selecting NumericWithScale values
     *
     */
    #[@test, @ignore('NumericWithScale not supported by MySQL')]
    public function selectNumericWithScaleZero() {
      parent::selectNumericWithScaleZero();
    }

    /**
     * Test selecting NumericWithScale values
     *
     */
    #[@test, @ignore('NumericWithScale not supported by MySQL')]
    public function selectNegativeNumericWithScale() {
      parent::selectNegativeNumericWithScale();
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test, @ignore('Numeric not supported by MySQL')]
    public function select64BitLongMaxPlus1Numeric() {
      parent::select64BitLongMaxPlus1Numeric();
    }

    /**
     * Test selecting numeric values
     *
     */
    #[@test, @ignore('Numeric not supported by MySQL')]
    public function select64BitLongMinMinus1Numeric() {
      parent::select64BitLongMinMinus1Numeric();
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test, @ignore('Decimal not supported by MySQL')]
    public function selectDecimalNull() {
      parent::selectDecimalNull();
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test, @ignore('Decimal not supported by MySQL')]
    public function selectDecimal() {
      parent::selectDecimal();
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test, @ignore('Decimal not supported by MySQL')]
    public function selectDecimalZero() {
      parent::selectDecimalZero();
    }

    /**
     * Test selecting Decimal values
     *
     */
    #[@test, @ignore('Decimal not supported by MySQL')]
    public function selectNegativeDecimal() {
      parent::selectNegativeDecimal();
    }

    /**
     * Test selecting DecimalWithScale values
     *
     */
    #[@test, @ignore('DecimalWithScale not supported by MySQL')]
    public function selectDecimalWithScaleNull() {
      parent::selectDecimalWithScaleNull();
    }

    /**
     * Test selecting DecimalWithScale values
     *
     */
    #[@test, @ignore('DecimalWithScale not supported by MySQL')]
    public function selectDecimalWithScale() {
      parent::selectDecimalWithScale();
    }

    /**
     * Test selecting DecimalWithScale values
     *
     */
    #[@test, @ignore('DecimalWithScale not supported by MySQL')]
    public function selectDecimalWithScaleZero() {
      parent::selectDecimalWithScaleZero();
    }

    /**
     * Test selecting DecimalWithScale values
     *
     */
    #[@test, @ignore('DecimalWithScale not supported by MySQL')]
    public function selectNegativeDecimalWithScale() {
      parent::selectNegativeDecimalWithScale();
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test, @ignore('Cast to float not supported by MySQL')]
    public function selectFloat() {
      parent::selectFloat();
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test, @ignore('Cast to float not supported by MySQL')]
    public function selectFloatOne() {
      parent::selectFloatOne();
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test, @ignore('Cast to float not supported by MySQL')]
    public function selectFloatZero() {
      parent::selectFloatZero();
    }

    /**
     * Test selecting Float values
     *
     */
    #[@test, @ignore('Cast to float not supported by MySQL')]
    public function selectNegativeFloat() {
      parent::selectNegativeFloat();
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test, @ignore('Cast to real not supported by MySQL')]
    public function selectReal() {
      parent::selectReal();
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test, @ignore('Cast to real not supported by MySQL')]
    public function selectRealOne() {
      parent::selectRealOne();
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test, @ignore('Cast to real not supported by MySQL')]
    public function selectRealZero() {
      parent::selectRealZero();
    }

    /**
     * Test selecting Real values
     *
     */
    #[@test, @ignore('Cast to real not supported by MySQL')]
    public function selectNegativeReal() {
      parent::selectNegativeReal();
    }

    /**
     * Test selecting varchar values
     *
     */
    #[@test, @ignore('Cast to varchar not supported by MySQL')]
    public function selectEmptyVarChar() {
      parent::selectEmptyVarChar();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to varchar not supported by MySQL')]
    public function selectVarChar() {
      parent::selectVarChar();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to varchar not supported by MySQL')]
    public function selectNullVarChar() {
      parent::selectNullVarChar();
    }

    /**
     * Test selecting money values
     *
     */
    #[@test, @ignore('Money not supported by MySQL')]
    public function selectMoney() {
      parent::selectMoney();
    }

    /**
     * Test selecting money values
     *
     */
    #[@test, @ignore('Money not supported by MySQL')]
    public function selectHugeMoney() {
      parent::selectHugeMoney();
    }

    /**
     * Test selecting money values
     *
     */
    #[@test, @ignore('Money not supported by MySQL')]
    public function selectMoneyOne() {
      parent::selectMoneyOne();
    }

    /**
     * Test selecting money values
     *
     */
    #[@test, @ignore('Money not supported by MySQL')]
    public function selectMoneyZero() {
      parent::selectMoneyZero();
    }

    /**
     * Test selecting money values
     *
     */
    #[@test, @ignore('Money not supported by MySQL')]
    public function selectNegativeMoney() {
      parent::selectNegativeMoney();
    }

    /**
     * Test selecting text values
     *
     */
    #[@test, @ignore('Cast to text not supported by MySQL')]
    public function selectEmptyText() {
      parent::selectEmptyText();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to text not supported by MySQL')]
    public function selectText() {
      parent::selectText();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to text not supported by MySQL')]
    public function selectUmlautText() {
      parent::selectUmlautText();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to text not supported by MySQL')]
    public function selectNulltext() {
      parent::selectNulltext();
    }

    /**
     * Test selecting Image values
     *
     */
    #[@test, @ignore('Cast to Image not supported by MySQL')]
    public function selectEmptyImage() {
      parent::selectEmptyImage();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to Image not supported by MySQL')]
    public function selectImage() {
      parent::selectImage();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to Image not supported by MySQL')]
    public function selectUmlautImage() {
      parent::selectUmlautImage();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to Image not supported by MySQL')]
    public function selectNullImage() {
      parent::selectNullImage();
    }

    /**
     * Test selecting binary values
     *
     */
    #[@test, @ignore('Cast to binary not supported by MySQL')]
    public function selectEmptyBinary() {
      parent::selectEmptyBinary();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to binary not supported by MySQL')]
    public function selectBinary() {
      parent::selectBinary();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to binary not supported by MySQL')]
    public function selectUmlautBinary() {
      parent::selectUmlautBinary();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to binary not supported by MySQL')]
    public function selectNullBinary() {
      parent::selectNullBinary();
    }


    /**
     * Test selecting varbinary values
     *
     */
    #[@test, @ignore('Cast to varbinary not supported by MySQL')]
    public function selectEmptyVarBinary() {
      parent::selectEmptyVarBinary();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to varbinary not supported by MySQL')]
    public function selectVarBinary() {
      parent::selectVarBinary();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to varbinary not supported by MySQL')]
    public function selectUmlautVarBinary() {
      parent::selectUmlautVarBinary();
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @ignore('Cast to varbinary not supported by MySQL')]
    public function selectNullVarBinary() {
      parent::selectNullVarBinary();
    }

    /**
     * Test selecting char values. Overwritten from parent class, MySQL does
     * not pad the value.
     *
     */
    #[@test]
    public function selectEmptyChar() {
      $this->assertEquals('', $this->db()->query('select cast("" as char(4)) as value')->next('value'));
    }

    /**
     * Test selecting char values. Overwritten from parent class, MySQL does
     * not pad the value.
     *
     */
    #[@test]
    public function selectCharWithPadding() {
      $this->assertEquals('t', $this->db()->query('select cast("t" as char(4)) as value')->next('value'));
    }

    /**
     * Test 
     *
     */
    #[@test, @ignore('No known way to test this in MySQL')]
    public function readingRowFailsWithQuery() {
      parent::readingRowFailsWithQuery();
    }

    /**
     * Test 
     *
     */
    #[@test, @ignore('No known way to test this in MySQL')]
    public function readingRowFailsWithOpen() {
      parent::readingRowFailsWithOpen();
    }

    /**
     * Test selecting a signed int
     *
     */
    #[@test]
    public function selectSignedInt() {
      $this->assertEquals(1, $this->db()->query('select cast(1 as signed integer) as value')->next('value'));
    }

    /**
     * Test selecting an unsigned bigint
     *
     */
    #[@test, @ignore('MySQL does not know unsigned bigints')]
    public function selectMaxUnsignedBigInt() {
      parent::selectMaxUnsignedBigInt();
    }

    /**
     * Test selecting tinyint values
     *
     */
    #[@test, @ignore('Cast to tinyint not supported by MySQL')]
    public function selectTinyint() {
      parent::selectTinyint();
    }

    /**
     * Test selecting tinyint values
     *
     */
    #[@test, @ignore('Cast to tinyint not supported by MySQL')]
    public function selectTinyintOne() {
      parent::selectTinyintOne();
    }

    /**
     * Test selecting tinyint values
     *
     */
    #[@test, @ignore('Cast to tinyint not supported by MySQL')]
    public function selectTinyintZero() {
      parent::selectTinyintZero();
    }

    /**
     * Test selecting smallint values
     *
     */
    #[@test, @ignore('Cast to smallint not supported by MySQL')]
    public function selectSmallint() {
      parent::selectSmallint();
    }

    /**
     * Test selecting smallint values
     *
     */
    #[@test, @ignore('Cast to smallint not supported by MySQL')]
    public function selectSmallintOne() {
      parent::selectSmallintOne();
    }

    /**
     * Test selecting smallint values
     *
     */
    #[@test, @ignore('Cast to smallint not supported by MySQL')]
    public function selectSmallintZero() {
      parent::selectSmallintZero();
    }
  }
?>
