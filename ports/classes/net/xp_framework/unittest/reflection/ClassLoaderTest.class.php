<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'util.log.Traceable');

  /**
   * Test the XP default classloader
   *
   * @see      xp://lang.ClassLoader
   * @purpose  Testcase
   */
  class ClassLoaderTest extends TestCase {
    public
      $classLoader= NULL;
    
    /**
     * Helper method
     *
     * @param   string name
     * @param   &lang.XPClass class
     * @return  bool
     * @throws  unittest.AssertionFailedError
     */
    protected function assertXPClass($name, $class) {
      return (
        $this->assertClass($class, 'lang.XPClass') &&
        $this->assertEquals($name, $class->getName()) &&
        $this->assertEquals('lang.XPClass<'.$name.'>', $class->toString())
      );
    }
  
    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->classLoader= ClassLoader::getDefault();
      $this->assertXPClass('lang.ClassLoader', $this->classLoader->getClass());
    }
 
    /**
     * Loads a class that has been loaded before
     *
     */
    #[@test]
    public function loadClass() {
      $this->assertXPClass('lang.Object', $this->classLoader->loadClass('lang.Object'));
    }

    /**
     * Tests the findClass() method
     *
     */
    #[@test]
    public function findThisClass() {
      $this->assertEquals(
        $this->getClass()->getClassLoader(), 
        $this->classLoader->findClass($this->getClassName())
      );
    }

    /**
     * Tests the findClass() method
     *
     */
    #[@test]
    public function findNullClass() {
      $this->assertEquals(xp::null(), $this->classLoader->findClass(NULL));
    }

    /**
     * Loads a class that has *not* been loaded before. Makes sure the
     * static initializer is called.
     *
     */
    #[@test]
    public function initializerCalled() {
      $name= 'net.xp_framework.unittest.reflection.LoaderTestClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }

      $class= $this->classLoader->loadClass($name);
      $this->assertXPClass($name, $class);
      $this->assertTrue(LoaderTestClass::initializerCalled());
    }

    /**
     * Tests the loadClass() method throws a ClassNotFoundException when given
     * a name of a class that cannot be found. 
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function loadNonExistantClass() {
      $this->classLoader->loadClass('@@NON-EXISTANT@@');
    }

    /**
     * Tests the defineClass() method
     *
     */
    #[@test]
    public function defineClass() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      
      $class= ClassLoader::defineClass($name, 'Object', array(), '{
        public static $initializerCalled= FALSE;
        
        static function __static() { 
          self::$initializerCalled= TRUE; 
        }
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue(RuntimeDefinedClass::$initializerCalled);
    }

    /**
     * Tests defineClass() with a given interface
     *
     */
    #[@test]
    public function defineClassImplements() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClassWithInterface';
      $class= $this->classLoader->defineClass(
        $name, 
        'lang.Object',
        array('util.log.Traceable'),
        '{ public function setTrace($cat) { } }'
      );

      $this->assertTrue(is('util.log.Traceable', $class->newInstance()));
      $this->assertFalse(is('util.log.Observer', $class->newInstance()));
    }
     
    
    /**
     * Tests the defineClass() method for the situtation when the
     * parent class does not exist.
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function defineClassWithNonExistantParent() {
      $name= 'net.xp_framework.unittest.reflection.IllegalClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      $this->classLoader->defineClass($name, 'NON_EXISTANT_PARENT', NULL, '{}');
    }
  }
?>
