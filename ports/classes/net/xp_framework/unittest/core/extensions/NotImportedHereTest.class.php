<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase', 
    'lang.types.ArrayList',
    'net.xp_framework.unittest.core.extensions.ArrayListDemo'
  );

  /**
   * TestCase
   *
   * @see   xp://net.xp_framework.unittest.core.extensions.ArrayListExtensions
   * @see   xp://net.xp_framework.unittest.core.extensions.ArrayListDemo
   */
  class NotImportedHereTest extends TestCase {
  
    /**
     * Tests situation when ArrayListExtensions hasn't been imported here
     * but inside another class which is imported here.
     *
     */
    #[@test, @expect('lang.Error')]
    public function test() {
      create(new ArrayList(7, 0, 10, 1, -1))->sorted();
    }
  }
?>
