<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.TypeInstance',
    'xp.compiler.types.TypeReflection'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.TypeInstance
   */
  class TypeInstanceTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function runnableInterfaceInstanceHasToStringMethod() {
      $this->assertTrue(create(new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->hasMethod('toString'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function runnableInterfaceInstanceToString() {
      $m= create(new TypeInstance(new TypeReflection(XPClass::forName('lang.Runnable'))))->getMethod('toString');
      $this->assertEquals(new TypeName('string'), $m->returns);
    }
  }
?>
