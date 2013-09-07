<?php namespace net\xp_framework\unittest\core;

/**
 * Tests the "NULL-safe" xp::null.
 */
class NullTest extends \unittest\TestCase {

  #[@test]
  public function isNull() {
    $this->assertTrue(is(null, \xp::null()));
  }

  #[@test]
  public function isNotAnObject() {
    $this->assertFalse(is('lang.Generic', \xp::null()));
  }

  #[@test]
  public function typeOf() {
    $this->assertEquals('<null>', \xp::typeOf(\xp::null()));
  }
  
  #[@test]
  public function stringOf() {
    $this->assertEquals('<null>', \xp::stringOf(\xp::null()));
  }
  
  #[@test, @expect('lang.IllegalAccessException')]
  public function newInstance() {
    new \null();
  }

  #[@test, @expect('lang.NullPointerException')]
  public function cloneNull() {
    clone(\xp::null());
  }

  #[@test, @expect('lang.NullPointerException')]
  public function methodInvocation() {
    $null= \xp::null();
    $null->method();
  }

  #[@test, @expect('lang.NullPointerException')]
  public function memberReadAccess() {
    $null= \xp::null();
    $i= $null->member;
  }
  
  #[@test, @expect('lang.NullPointerException')]
  public function memberWriteccess() {
    $null= \xp::null();
    $null->member= 15;
  }
}
