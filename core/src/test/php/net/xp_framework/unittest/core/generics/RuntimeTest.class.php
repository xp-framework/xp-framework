<?php namespace net\xp_framework\unittest\core\generics;

use unittest\TestCase;
use lang\types\String;
use lang\types\Integer;

/**
 * TestCase for generic behaviour at runtime.
 *
 * @see   xp://collections.Lookup
 */
class RuntimeTest extends TestCase {
  protected $fixture= null;
  
  /**
   * Creates fixture, a Lookup with String and TestCase as component
   * types.
   */  
  public function setUp() {
    $this->fixture= create('new net.xp_framework.unittest.core.generics.Lookup<String, TestCase>()');
  }

  #[@test]
  public function name() {
    $this->assertTrue(class_exists(
      'net\\xp_framework\\unittest\\core\\generics\\Lookup··String¸TestCase',
      false
    ));
  }

  #[@test]
  public function putStringAndThis() {
    $this->fixture->put(new String($this->name), $this);
  }

  #[@test]
  public function putAndGetRoundTrip() {
    $key= new String($this->name);
    $this->fixture->put($key, $this);
    $this->assertEquals($this, $this->fixture->get($key));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function keyTypeIncorrect() {
    $this->fixture->put(new Integer(1), $this);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function valueTypeIncorrect() {
    $this->fixture->put(new String($this->name), new \lang\Object());
  }
}
