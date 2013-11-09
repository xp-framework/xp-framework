<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test tokenizers for MySQL based connections
 *
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
abstract class MySQLTokenizerTest extends \net\xp_framework\unittest\rdbms\TokenizerTest {

  #[@test]
  public function labelToken() {
    $this->assertEquals(
      'select * from `order`',
      $this->fixture->prepare('select * from %l', 'order')
    );
  }

  #[@test]
  public function backslash() {
    $this->assertEquals(
      'select \'Hello \\\\ \' as strval',
      $this->fixture->prepare('select %s as strval', 'Hello \\ ')
    );
  }
}
