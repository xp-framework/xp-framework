<?php namespace net\xp_framework\unittest\rdbms\drivers;
 
/**
 * Test tokenizers for TDS based connections
 *
 * @see   xp://net.xp_framework.unittest.rdbms.TokenizerTest
 */
abstract class TDSTokenizerTest extends \net\xp_framework\unittest\rdbms\TokenizerTest {

  #[@test]
  public function dateToken() {
    $t= new \util\Date('1977-12-14');
    $this->assertEquals(
      "select * from news where date= '1977-12-14 12:00:00AM'",
      $this->fixture->prepare('select * from news where date= %s', $t)
    );
  }

  #[@test]
  public function timeStampToken() {
    $t= create(new \util\Date('1977-12-14'))->getTime();
    $this->assertEquals(
      "select * from news where created= '1977-12-14 12:00:00AM'",
      $this->fixture->prepare('select * from news where created= %u', $t)
    );
  }

  #[@test]
  public function dateArrayToken() {
    $d1= new \util\Date('1977-12-14');
    $d2= new \util\Date('1977-12-15');
    $this->assertEquals(
      "select * from news where created in ('1977-12-14 12:00:00AM', '1977-12-15 12:00:00AM')",
      $this->fixture->prepare('select * from news where created in (%s)', array($d1, $d2))
    );
  }
}
