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
     * Tests the defineClass() method
     *
     */
    #[@test]
    public function defineClass() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      
      $class= ClassLoader::getDefault()->defineClass($name, 'class RuntimeDefinedClass extends Object {
        public static $initializerCalled= FALSE;
        
        static function __static() { 
          self::$initializerCalled= TRUE; 
        }
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue(RuntimeDefinedClass::$initializerCalled);
    }
    
    /**
     * Tests the defineClass() method with a package-scope
     * ClassLoader (the defined class must be in the package
     * provided by the ClassLoader).
     *
     */
    #[@test]
    public function defineClassWithPrefix() {
      $cl= new ClassLoader('net.xp_framework.unittest.reflection.subpackage');
      $name= 'RuntimeDefinedClass2';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }

      $class= $cl->defineClass($name, 'class RuntimeDefinedClass2 extends Object {
        public static $initializerCalled= FALSE;
        
        static function __static() { 
          self::$initializerCalled= TRUE; 
        }
      }');
      $this->assertXPClass($cl->classpath.$name, $class);
      $this->assertTrue(RuntimeDefinedClass2::$initializerCalled);
    }
    
    /**
     * Test defineClass() method with the new signature
     *
     */
    #[@test]
    public function defineClassNG() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClass3';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      
      $class= ClassLoader::getDefault()->defineClass($name, 'lang.Object', NULL, '{
        public static $initializerCalled= FALSE;
        
        static function __static() { 
          self::$initializerCalled= TRUE; 
        }
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue(RuntimeDefinedClass3::$initializerCalled);
    }
    
    /**
     * Test defineClass() method with the new signature and a package-
     * scope ClassLoader (the defined class must be in the package
     * provided by the ClassLoader).
     *
     */
    #[@test]
    public function defineClassNGWithPrefix() {
      $cl= new ClassLoader('net.xp_framework.unittest.reflection.subpackage');
      $name= 'RuntimeDefinedClass4';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      
      $class= $cl->defineClass($name, 'lang.Object', NULL, '{
        public static $initializerCalled= FALSE;
        
        static function __static() { 
          self::$initializerCalled= TRUE; 
        }
      }');
      $this->assertXPClass($cl->classpath.$name, $class);
      $this->assertTrue(RuntimeDefinedClass4::$initializerCalled);
    }
    
    /**
     * Tests defineClass() with a given interface
     *
     */
    #[@test]
    public function defineClassImplements() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClassWithInterface';
      $class= ClassLoader::getDefault()->defineClass(
        $name, 
        'lang.Object',
        array('util.log.Traceable'),
        '{ public function setTrace($cat) { } }'
      );

      $this->assertTrue(is('util.log.Traceable', $class->newInstance()));
      $this->assertFalse(is('util.log.Observer', $class->newInstance()));
    }
     
    
    /**
     * Tests the defineClass() method for the situtation where the bytes 
     * argument failed to actually declare the class.
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function defineIllegalClass() {
      $name= 'net.xp_framework.unittest.reflection.IllegalClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      ClassLoader::getDefault()->defineClass($name, '1;');
    }
  }
?>
