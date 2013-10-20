<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\System;
use lang\SystemExit;

/**
 * TestCase for System exit
 *
 * @see      xp://lang.SystemExit
 */
class SystemExitTest extends TestCase {
  protected static $exiterClass= NULL;

  /**
   * Defines Exiter class
   */
  #[@beforeClass]
  public static function defineExiterClass() {
    self::$exiterClass= \lang\ClassLoader::defineClass('net.xp_framework.unittest.core.Exiter', 'lang.Object', array(), '{
      public function __construct() { throw new SystemExit(0); }
      public static function doExit() { new self(); }
    }');
  }

  #[@test]
  public function noStack() {
    $this->assertEquals(array(), create(new SystemExit(0))->getStackTrace());
  }

  #[@test]
  public function zeroExitCode() {
    $this->assertEquals(0, create(new SystemExit(0))->getCode());
  }

  #[@test]
  public function nonZeroExitCode() {
    $this->assertEquals(1, create(new SystemExit(1))->getCode());
  }

  #[@test]
  public function noMessage() {
    $this->assertEquals('', create(new SystemExit(0))->getMessage());
  }
  
  #[@test]
  public function message() {
    $this->assertEquals('Hello', create(new SystemExit(1, 'Hello'))->getMessage());
  }
  
  #[@test]
  public function invoke() {
    try {
      self::$exiterClass->getMethod('doExit')->invoke(NULL);
      $this->fail('Expected', NULL, 'lang.SystemExit');
    } catch (SystemExit $e) {
      // OK
    }
  }

  #[@test]
  public function construct() {
    try {
      self::$exiterClass->newInstance();
      $this->fail('Expected', NULL, 'lang.SystemExit');
    } catch (SystemExit $e) {
      // OK
    }
  }

  #[@test]
  public function systemExit() {
    try {
      \lang\Runtime::halt();
      $this->fail('Expected', NULL, 'lang.SystemExit');
    } catch (SystemExit $e) {
      $this->assertEquals(0, $e->getCode());
    }
  }

  #[@test]
  public function systemExitWithNonZeroExitCode() {
    try {
      \lang\Runtime::halt(127);
      $this->fail('Expected', NULL, 'lang.SystemExit');
    } catch (SystemExit $e) {
      $this->assertEquals(127, $e->getCode());
    }
  }
}
