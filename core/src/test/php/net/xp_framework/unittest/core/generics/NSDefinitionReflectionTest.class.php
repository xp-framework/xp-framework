<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.core.generics.NSLookup',
    'net.xp_framework.unittest.core.generics.AbstractDefinitionReflectionTest'
  );

  /**
   * TestCase for definition reflection using namespaces
   *
   * @see   xp://net.xp_framework.unittest.core.generics.NSLookup
   */
  class NSDefinitionReflectionTest extends AbstractDefinitionReflectionTest {
    
    /**
     * Creates fixture, a Lookup class
     *
     * @return  lang.XPClass
     */  
    protected function fixtureClass() {
      return XPClass::forName('net.xp_framework.unittest.core.generics.NSLookup');
    }

    /**
     * Creates fixture instance
     *
     * @return  var
     */
    protected function fixtureInstance() {
      return create('new net.xp_framework.unittest.core.generics.NSLookup<String, TestCase>()');
    }
  }
?>
