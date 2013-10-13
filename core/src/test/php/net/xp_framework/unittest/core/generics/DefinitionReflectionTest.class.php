<?php namespace net\xp_framework\unittest\core\generics;

/**
 * TestCase for definition reflection
 *
 * @see   xp://net.xp_framework.unittest.core.generics.Lookup
 */
class DefinitionReflectionTest extends AbstractDefinitionReflectionTest {
  
  /**
   * Creates fixture, a Lookup class
   *
   * @return  lang.XPClass
   */  
  protected function fixtureClass() {
    return \lang\XPClass::forName('net.xp_framework.unittest.core.generics.Lookup');
  }

  /**
   * Creates fixture instance
   *
   * @return  var
   */
  protected function fixtureInstance() {
    return create('new net.xp_framework.unittest.core.generics.Lookup<String, TestCase>()');
  }
}
