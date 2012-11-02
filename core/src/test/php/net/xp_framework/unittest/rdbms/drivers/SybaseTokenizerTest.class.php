<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.sybase.SybaseConnection',
    'net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest'
  );

  /**
   * Test sybase tokenizer
   *
   * @see   xp://rdbms.sybase.SybaseConnection
   * @see   xp://net.xp_framework.unittest.rdbms.drivers.TDSTokenizerTest
   */
  class SybaseTokenizerTest extends TDSTokenizerTest {
      
    /**
     * Sets up a Database Object for the test
     *
     * @return  rdbms.DBConnection
     */
    protected function fixture() {
      return new SybaseConnection(new DSN('sybase://localhost:1999/'));
    }
  }
?>
