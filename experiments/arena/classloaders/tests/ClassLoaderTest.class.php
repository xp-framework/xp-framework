<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'util.log.Traceable');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ClassLoaderTest extends TestCase {

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
      if (class_exists(xp::reflect($name))) {
        $this->fail('Class "'.$name.'" may not exist!');
      }
      return ClassLoader::getDefault()->defineClass($name, $parent, $interfaces, $bytes);
    }

    /**
     * Test "ClassOne" class is loaded from file system
     *
     */
    #[@test]
    public function fileSystemClassLoader() {
      $cl= XPClass::forName('tests.classes.ClassOne')->getClassLoader();
      $this->assertClass($cl, 'lang.FileSystemClassLoader');
    }
  
    /**
     * Test class loaders are equal for two classes loaded from the
     * file system.
     *
     */
    #[@test]
    public function twoClassesFromFileSystem() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassOne')->getClassLoader(),
        XPClass::forName('tests.classes.ClassTwo')->getClassLoader()
      );
    }

    /**
     * Test "ClassThree" is loaded from the archive in "lib"
     *
     */
    #[@test]
    public function archiveClassLoader() {
      $cl= XPClass::forName('tests.classes.ClassThree')->getClassLoader();
      $this->assertClass($cl, 'lang.archive.ArchiveClassLoader');
    }

    /**
     * Test  class loaders are equal for two classes loaded from the
     * archive in "lib"
     *
     */
    #[@test]
    public function twoClassesFromArchive() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassThree')->getClassLoader(),
        XPClass::forName('tests.classes.ClassFour')->getClassLoader()
      );
    }

    /**
     * Loads a class that has been loaded before
     *
     */
    #[@test]
    public function loadClass() {
      $this->assertXPClass('lang.Object', ClassLoader::getDefault()->loadClass('lang.Object'));
    }

    /**
     * Tests the findClass() method
     *
     */
    #[@test]
    public function findThisClass() {
      $this->assertEquals(
        $this->getClass()->getClassLoader(),
        ClassLoader::getDefault()->findClass($this->getClassName())
      );
    }

    /**
     * Tests the findClass() method
     *
     */
    #[@test]
    public function findNullClass() {
      $this->assertEquals(xp::null(), ClassLoader::getDefault()->findClass(NULL));
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

      $class= ClassLoader::getDefault()->loadClass($name);
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
      ClassLoader::getDefault()->loadClass('@@NON-EXISTANT@@');
    }

    /**
     * Test defineClass() method with the new signature
     *
     */
    #[@test]
    public function defineRuntimeClass() {
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
    public function defineRuntimeClassImplements() {
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
  }
?>
