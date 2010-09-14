<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase', 
    'lang.types.ArrayList',
    'net.xp_framework.unittest.core.extensions.ExtensionInvocationTest'
  );

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
?>
