<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.GenericArrays'
  );

  /**
   * TestCase
   *
   * @see   xp://net.xp_framework.unittest.core.GenericSerializer
   */
  class StaticGenericMethodInvocationTest extends TestCase {
    
    /**
     * Test
     *
     */
    #[@test]
    public function valueOfInt() {
      $list= invoke('GenericArrays::asList<int>', 1, 2, 3);
      $this->assertInstanceOf('util.collections.Vector<int>', $list);
      $this->assertEquals(array(1, 2, 3), $list->elements());
    }
  }
?>
