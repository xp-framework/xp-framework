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
     * @param   lang.XPClass class
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
     * Test defineClass() method
     *
     */
    #[@test]
    public function defineClassInNewPackage() {
      $name= 'net.xp_framework.unittest.reflection.dynclass1.ClassInNewPackage1';
      $class= $this->defineClass($name, 'lang.Object', NULL, '{}');

      $this->assertEquals(array($name), $class->getPackage()->getClassNames());
    }

    /**
     * Test defineClass() method
     *
     */
    #[@test]
    public function defineClassInNewPackageIndirect() {
      $name= 'net.xp_framework.unittest.reflection.dynclass2.ClassInNewPackage2';
      $class= $this->defineClass($name, 'lang.Object', NULL, '{}');

      $this->assertEquals(array($name), Package::forName($class->getPackage()->getName())->getClassNames());
    }

    /**
     * Test defineInterface() method
     *
     */
    #[@test]
    public function defineInterfaceInNewPackage() {
      $name= 'net.xp_framework.unittest.reflection.dyninterface1.InterfaceInNewPackage1';
      $class= $this->defineInterface($name, array(), '{}');

      $this->assertEquals(array($name), $class->getPackage()->getClassNames());
    }

    /**
     * Test defineInterface() method
     *
     */
    #[@test]
    public function defineInterfaceInNewPackageIndirect() {
      $name= 'net.xp_framework.unittest.reflection.dyninterface2.InterfaceInNewPackage2';
      $class= $this->defineInterface($name, array(), '{}');

      $this->assertEquals(array($name), Package::forName($class->getPackage()->getName())->getClassNames());
    }
  }
?>
