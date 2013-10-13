<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
use rdbms\pgsql\PostgreSQLConnection;
use net\xp_framework\unittest\rdbms\TokenizerTest;


/**
 * Test tokenizers for PostgreSQL connections
 *
 * @see   xp://rdbms.pgsql.PostgreSQLConnection
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
class PostgreSQLTokenizerTest extends TokenizerTest {

  /**
   * Sets up a Database Object for the test
   *
   * @return  rdbms.DBConnection
   */
  protected function fixture() {
    return new PostgreSQLConnection(new \rdbms\DSN('pgsql://localhost/'));
  }

  /**
   * Test label token
   *
   */
  #[@test]
  public function labelToken() {
    $this->assertEquals(
      'select * from "order"',
      $this->fixture->prepare('select * from %l', 'order')
    );
  }
}
