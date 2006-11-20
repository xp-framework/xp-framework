<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'lang.reflect.Proxy');

  /**
   * Tests the Proxy class
   *
   * @see      xp://lang.reflect.Proxy
   * @purpose  Unit test
   */
  class ProxyTest extends TestCase {
    var
      $handler       = NULL,
      $iteratorClass = NULL,
      $observerClass = NULL;

    /**
     * Setup method 
     *
     * @access  public
     */
    function setUp() {
      $cl= &ClassLoader::getDefault();
      $class= &$cl->defineClass(
        'net.xp_framework.unittest.reflection.DebugInvocationHandler', 
        'class DebugInvocationHandler extends Object {
           var $invocations= array();

           function invoke(&$proxy, $method, $args) { 
             $this->invocations[$method."_".sizeof($args)]= $args;
           }
        } implements("DebugInvocationHandler.class.php", "lang.reflect.InvocationHandler");
        '
      );
      $this->handler= &$class->newInstance();
      $this->iteratorClass= &XPClass::forName('util.Iterator');
      $this->observerClass= &XPClass::forName('util.Observer');
    }

    /**
     * Helper method which returns a proxy instance for a given list of
     * interfaces, using the default classloader and the handler defined
     * in setUp()
     *
     * @access  protected
     * @param   lang.XPClass[] interfaces
     * @return  &lang.reflect.Proxy
     */
    function &proxyInstanceFor($interfaces) {
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
     * @access  protected
     * @param   lang.XPClass[] interfaces
     * @return  &lang.XPClass
     */
    function &proxyClassFor($interfaces) {
      return Proxy::getProxyClass(
        ClassLoader::getDefault(),
        $interfaces,
        $this->handler
      );
    }

    /**
     * Tests Proxy classes are prefixed to make them unique. The prefix
     * is a constant defined in the Proxy class.
     *
     * @access  public
     */
    #[@test]
    function proxyClassNamesGetPrefixed() {
      $class= &$this->proxyClassFor(array($this->iteratorClass));
      $this->assertEquals(PROXY_PREFIX, substr($class->getName(), 0, strlen(PROXY_PREFIX)));
    }

    /**
     * Tests calling getProxyClass() twice with the same interface list
     * will result in the same proxy class
     *
     * @access  public
     */
    #[@test]
    function classesEqualForSameInterfaceList() {
      $c1= &$this->proxyClassFor(array($this->iteratorClass));
      $c2= &$this->proxyClassFor(array($this->iteratorClass));
      $c3= &$this->proxyClassFor(array($this->iteratorClass, $this->observerClass));

      $this->assertEquals($c1, $c2);
      $this->assertNotEquals($c1, $c3);
    }

    /**
     * Tests Proxy implements the interface(s) passed
     *
     * @access  public
     */
    #[@test]
    function iteratorInterfaceIsImplemented() {
      $class= &$this->proxyClassFor(array($this->iteratorClass));
      $interfaces= $class->getInterfaces();
      $this->assertEquals(1, sizeof($interfaces));
      $this->assertEquals($this->iteratorClass, $interfaces[0]);
    }

    /**
     * Tests Proxy implements the interface(s) passed
     *
     * @access  public
     */
    #[@test]
    function allInterfacesAreImplemented() {
      $class= &$this->proxyClassFor(array($this->iteratorClass, $this->observerClass));
      $interfaces= $class->getInterfaces();
      $this->assertEquals(2, sizeof($interfaces));
      $this->assertIn($interfaces, $this->iteratorClass);
      $this->assertIn($interfaces, $this->observerClass);
    }

    /**
     * Tests Proxy implements all Iterator methods
     *
     * @access  public
     */
    #[@test]
    function iteratorMethods() {
      $expected= array(
        'hashcode', 'equals', 'getclassname', 'getclass', 'tostring', // lang.Object
        'getproxyclass', 'newproxyinstance',                          // lang.reflect.Proxy
        'hasnext', 'next'                                             // util.Iterator
      );
      
      $class= &$this->proxyClassFor(array($this->iteratorClass));
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
     * @access  public
     */
    #[@test]
    function iteratorNextInvoked() {
      $proxy= &$this->proxyInstanceFor(array($this->iteratorClass));
      $proxy->next();
      $this->assertEquals(array(), $this->handler->invocations['next_0']);
    }
    
    /**
     * Tests proxies can not be created for classes, only for interfaces
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function cannotCreateProxiesForClasses() {
      $this->proxyInstanceFor(array(XPClass::forName('lang.Object')));
    }
    
    /**
     * Check that implementing two interfaces that declare a common
     * method does not issue a fatal error.
     *
     * @access  public
     */
    #[@test]
    function allowDoubledInterfaceMethod() {
      $cl= &ClassLoader::getDefault();
      $newIteratorClass= &$cl->defineClass('util.NewIterator', 'class NewIterator extends Iterator {
        function next() { }
      }');
      
      $this->proxyInstanceFor(array(
        XPClass::forName('util.Iterator'),
        XPClass::forName('util.NewIterator')
      ));
    }
    
    /**
     * Check that overloaded methods are correctly built.
     *
     * @access  public
     */
    #[@test]
    function overloadedMethod() {
      $proxy= &$this->proxyInstanceFor(array(XPClass::forName('net.xp_framework.unittest.reflection.OverloadedInterface')));
      $proxy->overloaded('foo');
      $proxy->overloaded('foo', 'bar');
      $this->assertEquals(array('foo'), $this->handler->invocations['overloaded_1']);
      $this->assertEquals(array('foo', 'bar'), $this->handler->invocations['overloaded_2']);
    }    
  }
?>
