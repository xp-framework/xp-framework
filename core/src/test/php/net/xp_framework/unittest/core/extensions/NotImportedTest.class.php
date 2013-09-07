<?php namespace net\xp_framework\unittest\core\extensions;

use unittest\TestCase;
use lang\types\ArrayList;

/**
 * TestCase
 *
 * @see   xp://net.xp_framework.unittest.core.extensions.ArrayListExtensions
 */
class NotImportedTest extends TestCase {

  /**
   * Tests situation when ArrayListExtensions hasn't been imported
   *
   */
  #[@test, @expect('lang.Error')]
  public function test() {
    create(new ArrayList(7, 0, 10, 1, -1))->sorted();
  }
}
