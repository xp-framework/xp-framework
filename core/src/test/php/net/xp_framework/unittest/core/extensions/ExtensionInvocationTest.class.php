<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase', 
    'lang.types.ArrayList',
    'net.xp_framework.unittest.core.extensions.ArrayListExtensions'
  );

  /**
   * TestCase
   *
   * @see   xp://net.xp_framework.unittest.core.extensions.ArrayListExtensions
   */
  class ExtensionInvocationTest extends TestCase {
  
    /**
     * Test map() extension method
     *
     */
    #[@test]
    public function mapMethod() {
      $this->assertEquals(
        new ArrayList(2, 4, 6),
        create(new ArrayList(1, 2, 3))->map(create_function('$e', 'return $e * 2;'))
      );
    }

    /**
     * Test sorted() extension method
     *
     */
    #[@test]
    public function sortedMethod() {
      $this->assertEquals(
        new ArrayList(-1, 0, 1, 7, 10),
        create(new ArrayList(7, 0, 10, 1, -1))->sorted(SORT_NUMERIC)
      );
    }

    /**
     * Test invoking a non-existant extension method
     *
     */
    #[@test, @expect('lang.Error')]
    public function nonExistantExtensionMethod() {
      create(new ArrayList(1, 2, 3))->nonExistant();
    }
  }
?>
