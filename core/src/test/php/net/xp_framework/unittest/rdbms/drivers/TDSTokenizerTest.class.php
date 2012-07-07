<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('net.xp_framework.unittest.rdbms.TokenizerTest');

  /**
   * Test tokenizers for TDS based connections
   *
   * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
   */
  abstract class TDSTokenizerTest extends TokenizerTest {

    /**
     * Test date token
     *
     */
    #[@test]
    public function dateToken() {
      $t= new Date('1977-12-14');
      $this->assertEquals(
        "select * from news where date= '1977-12-14 12:00:00AM'",
        $this->fixture->prepare('select * from news where date= %s', $t)
      );
    }

    /**
     * Test timestamp token
     *
     */
    #[@test]
    public function timeStampToken() {
      $t= create(new Date('1977-12-14'))->getTime();
      $this->assertEquals(
        "select * from news where created= '1977-12-14 12:00:00AM'",
        $this->fixture->prepare('select * from news where created= %u', $t)
      );
    }

    /**
     * Test array of date token
     *
     */
    #[@test]
    public function dateArrayToken() {
      $d1= new Date('1977-12-14');
      $d2= new Date('1977-12-15');
      $this->assertEquals(
        "select * from news where created in ('1977-12-14 12:00:00AM', '1977-12-15 12:00:00AM')",
        $this->fixture->prepare('select * from news where created in (%s)', array($d1, $d2))
      );
    }
  }
?>
