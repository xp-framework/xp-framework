<?php namespace net\xp_framework\unittest\core;

use lang\Object;
use lang\ClassLoader;

/**
 * Tests with() functionality
 */
class WithTest extends \unittest\TestCase {
  protected static $closes= null;
  protected static $raises= null;

  #[@beforeClass]
  public static function defineCloseableSubclasses() {
    self::$closes= ClassLoader::defineClass('_WithTest_C0', 'lang.Object', array('lang.Closeable'), '{
      public $closed= false;
      public function close() { $this->closed= true; }
    }');
    self::$raises= ClassLoader::defineClass('_WithTest_C1', 'lang.Object', array('lang.Closeable'), '{
      public function close() { throw new IllegalArgumentException("Cannot close"); }
    }');
  }

  #[@test]
  public function backwards_compatible_usage_without_closure() {
    with ($f= new Object()); {
      $this->assertInstanceOf('lang.Object', $f);
    }
  }

  #[@test]
  public function new_usage_with_closure() {
    with (new Object(), function($f) {
      $this->assertInstanceOf('lang.Object', $f);
    });
  }

  #[@test]
  public function closeable_is_open_inside_block() {
    with (self::$closes->newInstance(), function($f) {
      $this->assertFalse($f->closed);
    });
  }

  #[@test]
  public function closeable_is_closed_after_block() {
    $f= self::$closes->newInstance();
    with ($f, function() {
      // NOOP
    });
    $this->assertTrue($f->closed);
  }

  #[@test]
  public function all_closeables_are_closed_after_block() {
    $a= self::$closes->newInstance();
    $b= self::$closes->newInstance();
    with ($a, $b, function() {
      // NOOP
    });
    $this->assertEquals(array(true, true), array($a->closed, $b->closed));
  }

  #[@test]
  public function all_closeables_are_closed_after_exception() {
    $a= self::$closes->newInstance();
    $b= self::$closes->newInstance();
    try {
      with ($a, $b, function() {
        throw new \lang\IllegalStateException('Test');
      });
      $this->fail('No exception thrown', null, 'lang.IllegalStateException');
    } catch (\lang\IllegalStateException $expected) {
      $this->assertEquals(array(true, true), array($a->closed, $b->closed));
    }
  }

  #[@test]
  public function exceptions_from_close_are_ignored() {
    with (self::$raises->newInstance(), function() {
      // NOOP
    });
  }

  #[@test]
  public function exceptions_from_close_are_ignored_and_subsequent_closes_executed() {
    $b= self::$closes->newInstance();
    with (self::$raises->newInstance(), $b, function() {
      // NOOP
    });
    $this->assertTrue($b->closed);
  }
}
