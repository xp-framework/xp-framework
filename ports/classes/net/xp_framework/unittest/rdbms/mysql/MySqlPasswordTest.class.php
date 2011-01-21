<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.mysqlx.MySqlPassword'
  );

  /**
   * TestCase
   *
   * @see     xp://rdbms.mysqlx.MySqlPassword
   */
  class MySqlPasswordTest extends TestCase {
  
    /**
     * Test PROTOCOL_40 (8 byte scramble buf)
     *
     */
    #[@test]
    public function protocol40() {
      $this->assertEquals(
        new Bytes("UAXNPP\\O"), 
        new Bytes(MysqlPassword::$PROTOCOL_40->scramble('hello', '12345678'))
      );
    }

    /**
     * Test PROTOCOL_41 (20 byte scramble buf)
     *
     */
    #[@test]
    public function protocol41() {
      $this->assertEquals(
        new Bytes("}PQn\016s\013\013\033\022\373\252\033\240\207o=\262\304\335"), 
        new Bytes(MysqlPassword::$PROTOCOL_41->scramble('hello', '12345678901234567890'))
      );
    }
  }
?>
