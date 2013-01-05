<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.mysql.MySQLConnection',
    'net.xp_framework.unittest.rdbms.drivers.MySQLTokenizerTest'
  );

  /**
   * Test default MySQL tokenizer
   *
   * @see   xp://rdbms.mysql.MySQLConnection
   * @see   xp://net.xp_framework.unittest.rdbms.drivers.MySQLTokenizerTest
   */
  class DefaultMySQLTokenizerTest extends MySQLTokenizerTest {
      
    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected function fixture() {
      return new MySQLConnection(new DSN('mysql://localhost/'));
    }
  }
?>
