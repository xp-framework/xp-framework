<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\Runnable;

/**
 * Tests cast() functionality
 *
 * @purpose  Unittest
 */
class CastingTest extends TestCase implements Runnable {

  /**
   * Runnable implementation
   *
   */
  public function run() { 
    // Intentionally empty
  }

  #[@test]
  public function newinstance() {
    $runnable= newinstance('lang.Runnable', array(), '{
      public function run() { return "RUN"; }
    }');
    $this->assertEquals('RUN', cast($runnable, 'lang.Runnable')->run());
  }

  #[@test]
  public function null() {
    $this->assertEquals(\xp::null(), cast(NULL, 'lang.Object'));
  }

  #[@test]
  public function thisClass() {
    $this->assertTrue($this === cast($this, $this->getClassName()));
  }

  #[@test]
  public function runnableInterface() {
    $this->assertTrue($this === cast($this, 'lang.Runnable'));
  }

  #[@test]
  public function parentClass() {
    $this->assertTrue($this === cast($this, 'unittest.TestCase'));
  }

  #[@test]
  public function objectClass() {
    $this->assertTrue($this === cast($this, 'lang.Object'));
  }

  #[@test]
  public function genericInterface() {
    $this->assertTrue($this === cast($this, 'lang.Generic'));
  }

  #[@test, @expect('lang.ClassCastException')]
  public function unrelated() {
    cast($this, 'lang.types.String');
  }

  #[@test, @expect('lang.ClassCastException')]
  public function subClass() {
    cast(new \lang\Object(), 'lang.types.String');
  }

  #[@test, @expect('lang.ClassNotFoundException')]
  public function nonExistant() {
    cast($this, '@@NON_EXISTANT_CLASS@@');
  }

  #[@test, @expect('lang.NullPointerException')]
  public function npe() {
    cast(NULL, 'lang.Runnable')->run();
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function primitive() {
    cast('primitive', 'lang.Object');
  }
}
