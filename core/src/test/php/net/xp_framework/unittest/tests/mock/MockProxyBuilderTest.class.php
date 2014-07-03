<?php namespace net\xp_framework\unittest\tests\mock;

use unittest\TestCase;
use unittest\mock\MockProxyBuilder;
use util\XPIterator;
use lang\XPClass;
use lang\ClassLoader;
use lang\reflect\InvocationHandler;

/**
 * Tests the Proxy class
 *
 * @see    xp://lang.reflect.Proxy
 */
class MockProxyBuilderTest extends TestCase {
  public
    $handler       = null,
    $iteratorClass = null,
    $observerClass = null;

  /**
   * Setup method 
   *
   */
  public function setUp() {
    $this->handler= newinstance('lang.reflect.InvocationHandler', array(), '{
      public $invocations= array();

      public function invoke($proxy, $method, $args) { 
        $this->invocations[$method."_".sizeof($args)]= $args;
      }
    }');
    $this->iteratorClass= XPClass::forName('util.XPIterator');
    $this->observerClass= XPClass::forName('util.Observer');
  }

  /**
   * Helper method which returns a proxy instance for a given list of
   * interfaces, using the default classloader and the handler defined
   * in setUp()
   *
   * @param   lang.XPClass[] interfaces
   * @return  lang.reflect.Proxy
   */
  protected function proxyInstanceFor($interfaces) {
    return MockProxyBuilder::newProxyInstance(
      ClassLoader::getDefault(),
      $interfaces, 
      $this->handler
    );
  }
  
  /**
   * Helper method which returns a proxy class for a given list of
   * interfaces, using the default classloader and the handler defined
   * in setUp()
   *
   * @param   lang.XPClass[] interfaces
   * @return  lang.XPClass
   */
  protected function proxyClassFor($interfaces) {
    return MockProxyBuilder::getProxyClass(
      ClassLoader::getDefault(),
      $interfaces,
      $this->handler
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nullClassLoader() {
    MockProxyBuilder::getProxyClass(null, array($this->iteratorClass));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nullInterfaces() {
    MockProxyBuilder::getProxyClass(ClassLoader::getDefault(), null);
  }

  #[@test]
  public function proxyClassNamesGetPrefixed() {
    $class= $this->proxyClassFor(array($this->iteratorClass));
    $this->assertEquals(MOCK_PROXY_PREFIX, substr($class->getName(), 0, strlen(MOCK_PROXY_PREFIX)));
  }

  #[@test]
  public function classesEqualForSameInterfaceList() {
    $c1= $this->proxyClassFor(array($this->iteratorClass));
    $c2= $this->proxyClassFor(array($this->iteratorClass));
    $c3= $this->proxyClassFor(array($this->iteratorClass, $this->observerClass));

    $this->assertEquals($c1, $c2);
    $this->assertNotEquals($c1, $c3);
  }

  #[@test]
  public function iteratorInterfaceIsImplemented() {
    $class= $this->proxyClassFor(array($this->iteratorClass));
    $interfaces= $class->getInterfaces();
    $this->assertEquals(3, sizeof($interfaces)); //lang.Generic, lang.reflect.IProxy, util.XPIterator
    $this->assertTrue(in_array($this->iteratorClass, $interfaces));
  }

  #[@test]
  public function allInterfacesAreImplemented() {
    $class= $this->proxyClassFor(array($this->iteratorClass, $this->observerClass));
    $interfaces= $class->getInterfaces();
    $this->assertEquals(4, sizeof($interfaces));
    $this->assertTrue(in_array($this->iteratorClass, $interfaces));
    $this->assertTrue(in_array($this->observerClass, $interfaces));
  }

  #[@test]
  public function iteratorMethods() {
    $expected= array(
      'hashcode', 'equals', 'getclassname', 'getclass', 'tostring', // lang.Object
      'hasnext', 'next'                                             // util.XPIterator
    );
    
    $class= $this->proxyClassFor(array($this->iteratorClass));
    $methods= $class->getMethods();

    $this->assertEquals(sizeof($expected), sizeof($methods));
    foreach ($methods as $method) {
      $this->assertTrue(
        in_array(strtolower($method->getName()), $expected), 
        'Unexpected method "'.$method->getName().'"'
      );
    }
  }

  #[@test]
  public function iteratorNextInvoked() {
    $proxy= $this->proxyInstanceFor(array($this->iteratorClass));

    $proxy->next();
    $this->assertEquals(array(), $this->handler->invocations['next_0']);
  }
  
  #[@test, @expect('lang.IllegalArgumentException')]
  public function cannotCreateProxiesForClasses() {
    $this->proxyInstanceFor(array(XPClass::forName('lang.Object')));
  }
  
  #[@test]
  public function allowDoubledInterfaceMethod() {
    $newIteratorClass= ClassLoader::defineInterface('util.NewIterator', 'util.XPIterator');
    
    $this->proxyInstanceFor(array(
      XPClass::forName('util.XPIterator'),
      XPClass::forName('util.NewIterator')
    ));
  }
  
  #[@test]
  public function overloadedMethod() {
    $proxy= $this->proxyInstanceFor(array(XPClass::forName('net.xp_framework.unittest.reflection.OverloadedInterface')));
    $proxy->overloaded('foo');
    $proxy->overloaded('foo', 'bar');
    $this->assertEquals(array('foo'), $this->handler->invocations['overloaded_1']);
    $this->assertEquals(array('foo', 'bar'), $this->handler->invocations['overloaded_2']);
  }

  #[@test]
  public function proxyClass_implements_IMockProxy() {
    $proxy= $this->proxyClassFor(array($this->iteratorClass));
    $interfaces= $proxy->getInterfaces();
    $this->assertTrue(in_array(XPClass::forName('unittest.mock.IMockProxy'), $interfaces));
  }

  #[@test]
  public function concrete_methods_should_not_be_changed_by_default() {
    $proxyBuilder= new MockProxyBuilder();
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      array(),
      XPClass::forName('net.xp_framework.unittest.tests.mock.AbstractDummy')
    );

    $proxy= $class->newInstance($this->handler);
    $this->assertEquals('concreteMethod', $proxy->concreteMethod());
  }

  #[@test]
  public function abstract_methods_should_delegated_to_handler() {
    $proxyBuilder= new MockProxyBuilder();
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      array(),
      XPClass::forName('net.xp_framework.unittest.tests.mock.AbstractDummy')
    );

    $proxy= $class->newInstance($this->handler);
    $proxy->abstractMethod();

    $this->assertArray($this->handler->invocations['abstractMethod_0']);
  }

  #[@test]
  public function with_overwriteAll_abstract_methods_should_delegated_to_handler() {
    $proxyBuilder= new MockProxyBuilder();
    $proxyBuilder->setOverwriteExisting(true);
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      array(),
      XPClass::forName('net.xp_framework.unittest.tests.mock.AbstractDummy')
    );

    $proxy= $class->newInstance($this->handler);
    $proxy->concreteMethod();
    $this->assertArray($this->handler->invocations['concreteMethod_0']);
  }

  #[@test]
  public function reserved_methods_should_not_be_overridden() {
    $proxyBuilder= new MockProxyBuilder();
    $proxyBuilder->setOverwriteExisting(true);
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      array(),
      XPClass::forName('net.xp_framework.unittest.tests.mock.AbstractDummy')
    );

    $proxy= $class->newInstance($this->handler);

    $proxy->equals(new \lang\Object());
    $this->assertFalse(isset($this->handler->invocations['equals_1']));
  }

  #[@test]
  public function namespaced_parameters_handled_correctly() {
    $class= $this->proxyClassFor(array(ClassLoader::defineInterface('net.xp_framework.unittest.test.mock.NSInterface', array(), '{
      public function fixture(\lang\types\Long $param);
    }')));
    $this->assertEquals(
      XPClass::forName('lang.types.Long'),
      this($class->getMethod('fixture')->getParameters(), 0)->getType()
    );
  }
}
