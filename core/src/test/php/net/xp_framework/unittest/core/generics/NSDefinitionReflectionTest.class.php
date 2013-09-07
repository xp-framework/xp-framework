<?php namespace net\xp_framework\unittest\core\generics;

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
    return \lang\XPClass::forName('net.xp_framework.unittest.core.generics.NSLookup');
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
