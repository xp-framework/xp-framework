<?php namespace net\xp_framework\unittest\core\extensions;

use unittest\TestCase;
use lang\types\ArrayList;
new \import('net.xp_framework.unittest.core.extensions.ArrayListExtensions');
new \import('net.xp_framework.unittest.core.extensions.ThrowableExtensions');

/**
 * TestCase
 *
 * @see   xp://net.xp_framework.unittest.core.extensions.ArrayListExtensions
 * @see   xp://net.xp_framework.unittest.core.extensions.ThrowableExtensions
 * @see   https://github.com/xp-framework/xp-framework/issues/137
 */
class ExtensionInvocationTest extends TestCase {

  #[@test]
  public function mapMethod() {
    $this->assertEquals(
      new ArrayList(2, 4, 6),
      create(new ArrayList(1, 2, 3))->map(create_function('$e', 'return $e * 2;'))
    );
  }

  #[@test]
  public function sortedMethod() {
    $this->assertEquals(
      new ArrayList(-1, 0, 1, 7, 10),
      create(new ArrayList(7, 0, 10, 1, -1))->sorted(SORT_NUMERIC)
    );
  }

  #[@test, @expect('lang.Error')]
  public function nonExistantExtensionMethod() {
    create(new ArrayList(1, 2, 3))->nonExistant();
  }

  #[@test]
  public function throwabeExtensions() {
    $t= new \lang\Throwable('Test');
    $this->assertNotEquals(array(), $t->getStackTrace());
    $t->clearStackTrace();
    $this->assertEquals(array(), $t->getStackTrace());
  }
}
