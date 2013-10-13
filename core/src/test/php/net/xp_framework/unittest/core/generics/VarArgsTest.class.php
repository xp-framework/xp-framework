<?php namespace net\xp_framework\unittest\core\generics;

/**
 * TestCase for generic construction behaviour at runtime.
 *
 * @see   xp://net.xp_framework.unittest.core.generics.ListOf
 */
class VarArgsTest extends \unittest\TestCase {

  #[@test]
  public function withArguments() {
    $this->assertEquals(
      array('Hello', 'World'),
      create('new net.xp_framework.unittest.core.generics.ListOf<string>', 'Hello', 'World')->elements()
    );
  }

  #[@test]
  public function withoutArguments() {
    $this->assertEquals(
      array(),
      create('new net.xp_framework.unittest.core.generics.ListOf<string>')->elements()
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function withIncorrectArguments() {
    create('new net.xp_framework.unittest.core.generics.ListOf<string>', 'Hello', 1);
  }

  #[@test]
  public function withAllOf() {
    $this->assertEquals(
      array('Hello', 'World'),
      create('new net.xp_framework.unittest.core.generics.ListOf<string>')->withAll('Hello', 'World')->elements()
    );
  }
}
