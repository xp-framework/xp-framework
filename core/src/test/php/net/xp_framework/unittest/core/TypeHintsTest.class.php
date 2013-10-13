<?php namespace net\xp_framework\unittest\core;

/**
 * Test type hints.
 */
class TypeHintsTest extends \unittest\TestCase {

  /**
   * Pass an object
   * 
   * @param  lang.Generic $o
   * @return lang.Generic
   */
  protected function pass(\lang\Generic $o) { return $o; }

  /**
   * Pass a nullable object
   * 
   * @param  lang.Generic $o
   * @return lang.Generic
   */
  protected function nullable(\lang\Generic $o= null) { return $o; }


  #[@test]
  public function pass_an_object() {
    $o= new \lang\Object();
    $this->assertEquals($o, $this->pass($o));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function pass_a_primitive() {
    $this->pass(1);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function pass_null() {
    $this->pass(null);
  }

  #[@test]
  public function pass_object_to_nullable() {
    $o= new \lang\Object();
    $this->assertEquals($o, $this->nullable($o));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function pass_a_primitive_to_nullable() {
    $this->nullable(1);
  }

  #[@test]
  public function pass_null_to_nullable() {
    $this->assertEquals(null, $this->nullable(null));
  }
}
