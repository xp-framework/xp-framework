<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('net.xp_framework.unittest.rdbms.TokenizerTest');

  /**
   * Test tokenizers for MySQL based connections
   *
   * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
   */
  abstract class MySQLTokenizerTest extends TokenizerTest {

    /**
     * Test label token
     *
     */
    #[@test]
    public function labelToken() {
      $this->assertEquals(
        'select * from `order`',
        $this->fixture->prepare('select * from %l', 'order')
      );
    }

    /**
     * Test backslash escaping
     *
     */
    #[@test]
    public function backslash() {
      $this->assertEquals(
        'select \'Hello \\\\ \' as strval',
        $this->fixture->prepare('select %s as strval', 'Hello \\ ')
      );
    }
  }
?>
