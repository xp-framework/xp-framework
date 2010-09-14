<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.ArrayFilter',
    'lang.types.String',
    'lang.types.Integer'
  );

  /**
   * TestCase for generic behaviour at runtime.
   *
   * @see   xp://net.xp_framework.unittest.core.generics.ArrayFilter
   */
  class net·xp_framework·unittest·core·generics·AnonymousInstanceTest extends TestCase {
    
    /**
     * Test an array filter returning all test methods
     *
     */
    #[@test]
    public function testMethods() {
      $testmethods= newinstance('net.xp_framework.unittest.core.generics.ArrayFilter<Method>', array(), '{
        protected function accept($e) {
          return $e->hasAnnotation("test");
        }
      }');
      $filtered= $testmethods->filter($this->getClass()->getMethods());
      $this->assertNotEquals(0, sizeof($filtered));
      $this->assertInstanceOf('lang.reflect.Method', $filtered[0]);
      $this->assertEquals('testMethods', $filtered[0]->getName());
    }
  }
?>
