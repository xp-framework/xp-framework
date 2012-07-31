<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.mssql.MsSQLConnection',
    'net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest'
  );

  /**
   * Test MSSQL tokenizer
   *
   * @see   xp://rdbms.mssql.MsSQLConnection
   * @see   xp://net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest
   */
  class MssqlTokenizerTest extends TDSTokenizerTest {
      
    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected function fixture() {
      return new MsSQLConnection(new DSN('mssql://localhost:1433/'));
    }
  }
?>
