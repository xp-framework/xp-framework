<?php namespace net\xp_framework\unittest\core\generics;

use lang\types\String;
use lang\types\Integer;


/**
 * TestCase for generic behaviour at runtime.
 *
 * @see   xp://net.xp_framework.unittest.core.generics.ArrayFilter
 */
class AnonymousInstanceTest extends \unittest\TestCase {

  #[@test]
  public function testMethods() {
    $testmethods= newinstance('net.xp_framework.unittest.core.generics.ArrayFilter<Method>', array(), '{
      protected function accept($e) {
        return $e->hasAnnotation("test");
      }
    }');
    $filtered= $testmethods->filter($this->getClass()->getMethods());
    $this->assertInstanceOf('lang.reflect.Method[]', $filtered);
    $this->assertNotEquals(0, sizeof($filtered));
    $this->assertEquals('testMethods', $filtered[0]->getName());
  }

  #[@test]
  public function classNameOfGeneric() {
    $instance= newinstance('util.collections.Vector<Object>', array(), '{
    }');
    $n= $instance->getClassName();
    $this->assertEquals(
      'util.collections.Vector··Object',
      substr($n, 0, strrpos($n, '·')),
      $n
    );
  }

  #[@test]
  public function classNameOfGenericInPackage() {
    $instance= newinstance('net.xp_framework.unittest.core.generics.ArrayFilter<Object>', array(), '{
      protected function accept($e) { return TRUE; }
    }');
    $n= $instance->getClassName();
    $this->assertEquals(
      'net.xp_framework.unittest.core.generics.ArrayFilter··Object',
      substr($n, 0, strrpos($n, '·')),
      $n
    );
  }
}
