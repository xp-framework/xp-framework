<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'unittest.mock.MockProxyBuilder',
    'util.XPIterator',
    'lang.reflect.InvocationHandler'
  );

  /**
   * Tests the Proxy class
   *
   * @see      xp://lang.reflect.Proxy
   * @purpose  Unit test
   */
  class MockProxyBuilderTest extends TestCase {
    public
      $handler       = NULL,
      $iteratorClass = NULL,
      $observerClass = NULL;

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

    /**
     * Tests passing NULL for classloader
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nullClassLoader() {
      MockProxyBuilder::getProxyClass(NULL, array($this->iteratorClass));
    }

    /**
     * Tests passing NULL for interfaces
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nullInterfaces() {
      MockProxyBuilder::getProxyClass(ClassLoader::getDefault(), NULL);
    }

    /**
     * Tests Proxy classes are prefixed to make them unique. The prefix
     * is a constant defined in the Proxy class.
     *
     */
    #[@test]
    public function proxyClassNamesGetPrefixed() {
      $class= $this->proxyClassFor(array($this->iteratorClass));
      $this->assertEquals(MOCK_PROXY_PREFIX, substr($class->getName(), 0, strlen(MOCK_PROXY_PREFIX)));
    }

    /**
     * Tests calling getProxyClass() twice with the same interface list
     * will result in the same proxy class
     *
     */
    #[@test]
    public function classesEqualForSameInterfaceList() {
      $c1= $this->proxyClassFor(array($this->iteratorClass));
      $c2= $this->proxyClassFor(array($this->iteratorClass));
      $c3= $this->proxyClassFor(array($this->iteratorClass, $this->observerClass));

      $this->assertEquals($c1, $c2);
      $this->assertNotEquals($c1, $c3);
    }

    /**
     * Tests Proxy implements the interface(s) passed
     *
     */
    #[@test]
    public function iteratorInterfaceIsImplemented() {
      $class= $this->proxyClassFor(array($this->iteratorClass));
      $interfaces= $class->getInterfaces();
      $this->assertEquals(3, sizeof($interfaces)); //lang.Generic, lang.reflect.IProxy, util.XPIterator
      $this->assertTrue(in_array($this->iteratorClass, $interfaces));
    }

    /**
     * Tests Proxy implements the interface(s) passed
     *
     */
    #[@test]
    public function allInterfacesAreImplemented() {
      $class= $this->proxyClassFor(array($this->iteratorClass, $this->observerClass));
      $interfaces= $class->getInterfaces();
      $this->assertEquals(4, sizeof($interfaces));
      $this->assertTrue(in_array($this->iteratorClass, $interfaces));
      $this->assertTrue(in_array($this->observerClass, $interfaces));
    }

    /**
     * Tests Proxy implements all Iterator methods
     *
     */
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

    /**
     * Tests util.Iterator::next() invocation without arguments
     *
     */
    #[@test]
    public function iteratorNextInvoked() {
      $proxy= $this->proxyInstanceFor(array($this->iteratorClass));

      $proxy->next();
      $this->assertEquals(array(), $this->handler->invocations['next_0']);
    }
    
    /**
     * Tests proxies can not be created for classes, only for interfaces
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotCreateProxiesForClasses() {
      $this->proxyInstanceFor(array(XPClass::forName('lang.Object')));
    }
    
    /**
     * Check that implementing two interfaces that declare a common
     * method does not issue a fatal error.
     *
     */
    #[@test]
    public function allowDoubledInterfaceMethod() {
      $newIteratorClass= ClassLoader::defineInterface('util.NewIterator', 'util.XPIterator');
      
      $this->proxyInstanceFor(array(
        XPClass::forName('util.XPIterator'),
        XPClass::forName('util.NewIterator')
      ));
    }
    
    /**
     * Check that overloaded methods are correctly built.
     *
     */
    #[@test]
    public function overloadedMethod() {
      $proxy= $this->proxyInstanceFor(array(XPClass::forName('net.xp_framework.unittest.reflection.OverloadedInterface')));
      $proxy->overloaded('foo');
      $proxy->overloaded('foo', 'bar');
      $this->assertEquals(array('foo'), $this->handler->invocations['overloaded_1']);
      $this->assertEquals(array('foo', 'bar'), $this->handler->invocations['overloaded_2']);
    }

    /**
     * A proxy class should be instance of IProxy
     *
     */
    #[@test]
    public function proxyClass_implements_IMockProxy() {
      $proxy= $this->proxyClassFor(array($this->iteratorClass));
      $interfaces= $proxy->getInterfaces();
      $this->assertTrue(in_array(XPClass::forName('unittest.mock.IMockProxy'), $interfaces));
    }

    /**
     * Test
     *
     */
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

    /**
     * Test
     *
     */
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

    /**
     * Test
     *
     */
    #[@test]
    public function with_overwriteAll_abstract_methods_should_delegated_to_handler() {
      $proxyBuilder= new MockProxyBuilder();
      $proxyBuilder->setOverwriteExisting(TRUE);
      $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
        array(),
        XPClass::forName('net.xp_framework.unittest.tests.mock.AbstractDummy')
      );

      $proxy= $class->newInstance($this->handler);
      $proxy->concreteMethod();
      $this->assertArray($this->handler->invocations['concreteMethod_0']);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function reserved_methods_should_not_be_overridden() {
      $proxyBuilder= new MockProxyBuilder();
      $proxyBuilder->setOverwriteExisting(TRUE);
      $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
        array(),
        XPClass::forName('net.xp_framework.unittest.tests.mock.AbstractDummy')
      );

      $proxy= $class->newInstance($this->handler);

      $proxy->equals(new Object());
      $this->assertFalse(isset($this->handler->invocations['equals_1']));
    }
  }
?>
