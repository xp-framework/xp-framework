<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'util.log.Traceable', 'lang.reflect.Proxy');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class RuntimeClassDefinitionTest extends TestCase {

    /**
     * Helper method
     *
     * @param   string name
     * @param   lang.XPClass class
     * @throws  unittest.AssertionFailedError
     */
    protected function assertXPClass($name, $class) {
      $this->assertClass($class, 'lang.XPClass');
      $this->assertEquals($name, $class->getName());
    }

    /**
     * Helper method
     *
     * @param   string name
     * @param   string parent
     * @param   string[] interfaces
     * @return  lang.XPClass class
     * @throws  unittest.AssertionFailedError
     */
    protected function defineClass($name, $parent, $interfaces, $bytes) {
      if (class_exists(xp::reflect($name), FALSE)) {
        $this->fail('Class "'.$name.'" may not exist!');
      }
      return ClassLoader::defineClass($name, $parent, $interfaces, $bytes);
    }

    /**
     * Helper method
     *
     * @param   string name
     * @param   lang.XPClass class
     * @throws  unittest.AssertionFailedError
     */
    protected function defineInterface($name, $parents, $bytes) {
      if (interface_exists(xp::reflect($name), FALSE)) {
        $this->fail('Interface "'.$name.'" may not exist!');
      }
      return ClassLoader::defineInterface($name, $parents, $bytes);
    }

    /**
     * Test defineClass() method
     *
     */
    #[@test]
    public function defineClassWithInitializer() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClass';
      $class= $this->defineClass($name, 'lang.Object', NULL, '{
        public static $initializerCalled= FALSE;
        
        static function __static() { 
          self::$initializerCalled= TRUE; 
        }
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue(RuntimeDefinedClass::$initializerCalled);
      $this->assertClass($class->getClassLoader(), 'lang.DynamicClassLoader');
    }
    
    /**
     * Tests defineClass() with a given interface
     *
     */
    #[@test]
    public function defineTraceableClass() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClassWithInterface';
      $class= $this->defineClass($name, 'lang.Object', array('util.log.Traceable'), '{
        public function setTrace($cat) { } 
      }');

      $this->assertTrue($class->isSubclassOf('util.log.Traceable'));
      $this->assertClass($class->getClassLoader(), 'lang.DynamicClassLoader');
    }

    /**
     * Tests newinstance()
     *
     */
    #[@test]
    public function newInstance() {
      $i= newinstance('lang.Object', array(), '{ public function bar() { return TRUE; }}');
      $this->assertClass($i->getClass()->getClassLoader(), 'lang.DynamicClassLoader');
    }

    /**
     * Tests Proxy
     *
     */
    #[@test]
    public function proxyInstance() {
      $c= Proxy::getProxyClass(
        ClassLoader::getDefault(), 
        array(XPClass::forName('util.log.Traceable'))
      );
      $this->assertClass($c->getClassLoader(), 'lang.DynamicClassLoader');
    }

    /**
     * Test defineInterface() method
     *
     */
    #[@test]
    public function defineSimpleInterface() {
      $name= 'net.xp_framework.unittest.reflection.SimpleInterface';
      $class= $this->defineInterface($name, array(), '{
        public function setTrace($cat);
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue($class->isInterface());
      $this->assertEquals(array(), $class->getInterfaces());
      $this->assertClass($class->getClassLoader(), 'lang.DynamicClassLoader');
    }

    /**
     * Test defineInterface() method
     *
     */
    #[@test]
    public function defineInterfaceWithParent() {
      $name= 'net.xp_framework.unittest.reflection.InterfaceWithParent';
      $class= $this->defineInterface($name, array('util.log.Traceable'), '{
        public function setDebug($cat);
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue($class->isInterface());
      $this->assertEquals(array(XPClass::forName('util.log.Traceable')), $class->getInterfaces());
      $this->assertClass($class->getClassLoader(), 'lang.DynamicClassLoader');
    }

    /**
     * Test defineInterface() method
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function defineInterfaceWithNonExistantParent() {
      $name= 'net.xp_framework.unittest.reflection.ErroneousInterface';
      $this->defineInterface($name, array('@@nonexistant@@'), '{
        public function setDebug($cat);
      }');
    }

    /**
     * Test default class loader
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/94
     */
    #[@test]
    public function defaultClassLoaderProvidesDefinedClass() {
      $class= 'net.xp_framework.unittest.reflection.lostandfound.CL1';
      $this->defineClass($class, 'lang.Object', array(), '{ }');

      $this->assertTrue(ClassLoader::getDefault()->providesClass($class));
    }

    /**
     * Test default class loader
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/94
     */
    #[@test]
    public function defaultClassLoaderProvidesDefinedInterface() {
      $class= 'net.xp_framework.unittest.reflection.lostandfound.IF1';
      $this->defineInterface($class, array(), '{ }');

      $this->assertTrue(ClassLoader::getDefault()->providesClass($class));
    }

    /**
     * Test default class loader
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/94
     */
    #[@test]
    public function defaultClassLoaderProvidesPackageOfDefinedClass() {
      $package= 'net.xp_framework.unittest.reflection.lostandfound';
      $this->defineClass($package.'.CL2', 'lang.Object', array(), '{ }');

      $this->assertTrue(ClassLoader::getDefault()->providesPackage($package));
    }
  }
?>
