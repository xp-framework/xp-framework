<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.ibase.InterBaseConnection',
    'net.xp_framework.unittest.rdbms.TokenizerTest'
  );

  /**
   * Test tokenizers for IBase connections
   *
   * @see   xp://rdbms.ibase.InterBaseConnection
   * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
   */
  class InterBaseTokenizerTest extends TokenizerTest {

    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected function fixture() {
      return new InterBaseConnection(new DSN('ibase://localhost/'));
    }
  }
?>
