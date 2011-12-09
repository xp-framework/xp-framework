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
  }
?>
