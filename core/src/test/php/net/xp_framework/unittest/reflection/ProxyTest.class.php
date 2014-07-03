<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;
use lang\reflect\Proxy;
use util\XPIterator;
use lang\XPClass;
use lang\ClassLoader;
use lang\reflect\InvocationHandler;

/**
 * Tests the Proxy class
 *
 * @see   xp://lang.reflect.Proxy
 */
class ProxyTest extends TestCase {
  public
    $handler       = null,
    $iteratorClass = null,
    $observerClass = null;

  /**
   * Setup method 
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
    return Proxy::newProxyInstance(
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
    return Proxy::getProxyClass(
      ClassLoader::getDefault(),
      $interfaces,
      $this->handler
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nullClassLoader() {
    Proxy::getProxyClass(null, array($this->iteratorClass));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function emptyInterfaces() {
    Proxy::getProxyClass(ClassLoader::getDefault(), array());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function nullInterfaces() {
    Proxy::getProxyClass(ClassLoader::getDefault(), null);
  }

  #[@test]
  public function proxyClassNamesGetPrefixed() {
    $class= $this->proxyClassFor(array($this->iteratorClass));
    $this->assertEquals(Proxy::PREFIX, substr($class->getName(), 0, strlen(Proxy::PREFIX)));
  }

  #[@test]
  public function classesEqualForSameInterfaceList() {
    $this->assertEquals(
      $this->proxyClassFor(array($this->iteratorClass)),
      $this->proxyClassFor(array($this->iteratorClass))
    );
  }

  #[@test]
  public function classesNotEqualForDifferingInterfaceList() {
    $this->assertNotEquals(
      $this->proxyClassFor(array($this->iteratorClass)),
      $this->proxyClassFor(array($this->iteratorClass, $this->observerClass))
    );
  }

  #[@test]
  public function iteratorInterfaceIsImplemented() {
    $class= $this->proxyClassFor(array($this->iteratorClass));
    $interfaces= $class->getInterfaces();
    $this->assertEquals(2, sizeof($interfaces));
    $this->assertEquals($this->iteratorClass, $interfaces[1]);
  }

  #[@test]
  public function allInterfacesAreImplemented() {
    $class= $this->proxyClassFor(array($this->iteratorClass, $this->observerClass));
    $interfaces= $class->getInterfaces();
    $this->assertEquals(3, sizeof($interfaces));
    $this->assertTrue(in_array($this->iteratorClass, $interfaces));
    $this->assertTrue(in_array($this->observerClass, $interfaces));
  }

  #[@test]
  public function iteratorMethods() {
    $expected= array(
      'hashcode', 'equals', 'getclassname', 'getclass', 'tostring', // lang.Object
      'getproxyclass', 'newproxyinstance',                          // lang.reflect.Proxy
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
  
  #[@test, @expect('lang.IllegalArgumentException')]
  public function cannotCreateProxiesForClassesAsSecondArg() {
    $this->proxyInstanceFor(array(
      XPClass::forName('util.XPIterator'),
      XPClass::forName('lang.Object')
    ));
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
  public function namespaced_parameters_handled_correctly() {
    $proxy= $this->proxyClassFor(array(ClassLoader::defineInterface('net.xp_framework.unittest.reflection.NSInterface', [], '{
      public function fixture(\lang\types\Long $param);
    }')));
    $this->assertEquals(
      XPClass::forName('lang.types.Long'),
      this($proxy->getMethod('fixture')->getParameters(), 0)->getType()
    );
  }
}
