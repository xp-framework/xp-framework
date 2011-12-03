<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * Sybase integration test
   *
   * @ext       sybase_ct
   */
  class SybaseIntegrationTest extends RdbmsIntegrationTest {
    
    /**
     * Before class method: set minimun server severity;
     * otherwise server messages end up on the error stack
     * and will let the test fail (no error policy).
     *
     */
    public function setUp() {
      parent::setUp();
      if (function_exists('sybase_min_server_severity')) {
        sybase_min_server_severity(12);
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
      return 'sybase';
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

    /**
     * Test selecting string values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyString() {
      $this->assertEquals(' ', $this->db()->query('select "" as value')->next('value'));
    }

    /**
     * Test selecting string values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyVarchar() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as varchar(255)) as value')->next('value'));
    }
  }
?>
