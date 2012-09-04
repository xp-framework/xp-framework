<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.mysqli.MySQLiConnection',
    'net.xp_framework.unittest.rdbms.drivers.MySQLTokenizerTest'
  );

  /**
   * Test MySQLi tokenizer
   *
   * @see   xp://rdbms.mysqli.MySQLiConnection
   * @see   xp://net.xp_framework.unittest.rdbms.drivers.MySQLTokenizerTest
   */
  class MySQLImprovedTokenizerTest extends MySQLTokenizerTest {
      
    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected function fixture() {
      return new MySQLiConnection(new DSN('mysqli://localhost/'));
    }
  }
?>
