<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.archive.Archive');

  /**
   * TestCase for classloading
   *
   * Makes use of the following classes in the package
   * net.xp_framework.unittest.reflection.classes:
   * <ul>
   *   <li>ClassOne, ClassTwo - exist in the same directory as this class</li>
   *   <li>ClassThree, ClassFour - exist in "lib/three-and-four.xar"</li>
   *   <li>ClassFive - exists in "contained.xar" within "lib/three-and-four.xar"</li>
   * </ul>
   *
   * @see      xp://lang.ClassLoader
   * @see      xp://lang.XPClass#getClassLoader
   * @purpose  Unittest
   */
  class ClassLoaderTest extends TestCase {
    protected
      $libraryLoader   = NULL,
      $brokenLoader    = NULL,
      $containedLoader = NULL;
      
    /**
     * Setup this test. Registeres class loaders deleates for the 
     * afforementioned XARs
     *
     */
    public function setUp() {
      $this->libraryLoader= ClassLoader::registerLoader(new ArchiveClassLoader(new Archive(XPClass::forName(xp::nameOf(__CLASS__))
        ->getPackage()
        ->getPackage('lib')
        ->getResourceAsStream('three-and-four.xar')
      )));
      $this->brokenLoader= ClassLoader::registerLoader(new ArchiveClassLoader(new Archive(XPClass::forName(xp::nameOf(__CLASS__))
        ->getPackage()
        ->getPackage('lib')
        ->getResourceAsStream('broken.xar')
      )));
      $this->containedLoader= ClassLoader::registerLoader(new ArchiveClassLoader(new Archive($this
        ->libraryLoader
        ->getResourceAsStream('contained.xar')
      )));
    }
    
    /**
     * Tear down this test. Removes classloader delegates registered 
     * during setUp()
     *
     */
    public function tearDown() {
      ClassLoader::removeLoader($this->libraryLoader);
      ClassLoader::removeLoader($this->containedLoader);
      ClassLoader::removeLoader($this->brokenLoader);
    }

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
     * Display some information
     *
     * Annotate this method w/ @test to retrieve debug information.
     */
    public function classLoaderInformation() {
      with ($p= Package::forName('net.xp_framework.unittest.reflection.classes')); {
        Console::writeLine('Object     : ', XPClass::forName('lang.Object')->getClassLoader());
        Console::writeLine('This       : ', $this->getClass()->getClassLoader());
        Console::writeLine('ClassOne   : ', $p->loadClass('ClassOne')->getClassLoader());
        Console::writeLine('ClassTwo   : ', $p->loadClass('ClassTwo')->getClassLoader());
        Console::writeLine('ClassThree : ', $p->loadClass('ClassThree')->getClassLoader());
        Console::writeLine('ClassFour  : ', $p->loadClass('ClassFour')->getClassLoader());
        Console::writeLine('ClassFive  : ', $p->loadClass('ClassFive')->getClassLoader());
      }
    }

    /**
     * Test "ClassOne" class is loaded from the same class loader 
     * as this class (it exists in the same directory).
     *
     */
    #[@test]
    public function sameClassLoader() {
      $this->assertEquals(
        XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getClassLoader(),
        $this->getClass()->getClassLoader()
      );
    }
  
    /**
     * Test class loaders are equal for two classes loaded from the
     * same place (both exist in the same directory).
     *
     */
    #[@test]
    public function twoClassesFromSamePlace() {
      $this->assertEquals(
        XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getClassLoader(),
        XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassTwo')->getClassLoader()
      );
    }

    /**
     * Test "ClassThree" is loaded from the archive in "lib"
     *
     */
    #[@test]
    public function archiveClassLoader() {
      $this->assertClass(
         XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree')->getClassLoader(),
        'lang.archive.ArchiveClassLoader'
      );
    }

    /**
     * Test "ClassFive" is loaded from an archive
     *
     */
    #[@test]
    public function containedArchiveClassLoader() {
      $this->assertClass(
         XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassFive')->getClassLoader(),
        'lang.archive.ArchiveClassLoader'
      );
    }

    /**
     * Test  class loaders are equal for two classes loaded from the
     * archive in "lib"
     *
     */
    #[@test]
    public function twoClassesFromArchive() {
      $this->assertEquals(
        XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree')->getClassLoader(),
        XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassFour')->getClassLoader()
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
      if (class_exists(xp::reflect($name), FALSE)) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }

      $this->assertXPClass($name, ClassLoader::getDefault()->loadClass($name));
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
     * Loads a class file that does not declare a class
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function loadClassFileWithoutDeclaration() {
      XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.NoClass');
    }

    /**
     * Loads a class file that does not declare a class
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function loadClassFileWithIncorrectDeclaration() {
      XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.FalseClass');
    }

    /**
     * Loads a class file that loads a file that is broken
     *
     */
    #[@test, @expect('lang.ClassDependencyException')]
    public function loadClassWithBrokenDependency() {
      XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.BrokenDependencyClass');
    }

    /**
     * Loads a class file whose class extends a class that cannot be
     * loaded and the class cannot be declared.
     *
     */
    #[@test, @expect('lang.ClassLinkageException')]
    public function loadClassWithMissingDefinition() {
      XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.MissingDefinitionClass');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function loadClassFileWithRecusionInStaticBlock() {
      with ($p= Package::forName('net.xp_framework.unittest.reflection.classes')); {
        $two= $p->loadClass('StaticRecursionTwo');
        $one= $p->loadClass('StaticRecursionOne');
        $this->assertEquals($two, $one->getField('two')->get(NULL));
      }
    }

    /**
     * Test (mis-)using the public constructor does not lead to a 
     * ReflectionException surfacing
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function newInstance() {
      new XPClass('DoesNotExist');
    }

    /**
     * Test (mis-)using the public constructor does not lead to a 
     * ReflectionException surfacing
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function newInstance__PHP_Incomplete_Class() {
      new XPClass(unserialize('O:12:"DoesNotExist":0:{}'));
    }
    
    /**
     * Test archive delivers the correct contents
     *
     */
    #[@test]
    public function packageContents() {
      $this->assertEquals(
        array('net/', 'META-INF/', 'contained.xar'),
        $this->libraryLoader->packageContents('')
      );
    }
    
    /**
     * Test archive correctly detects delivered packages
     *
     */
    #[@test]
    public function providesPackage() {
      $this->assertTrue($this->libraryLoader->providesPackage('net.xp_framework'));
    }
    
    
    /**
     * Test archive checks full path for package names
     *
     */
    #[@test]
    public function doesNotProvideAPackage() {
      $this->assertFalse($this->libraryLoader->providesPackage('net.xp_frame'));
    }

    /**
     * Test "Classone" class is not provided
     *
     * @see   https://github.com/xp-framework/xp-framework/pull/235
     */
    #[@test]
    public function doesNotProvideClassone() {
      $this->assertFalse(ClassLoader::getDefault()
        ->providesClass('net.xp_framework.unittest.reflection.classes.Classone')
      );
    }

    /**
     * Test "Classone" class is not provided
     *
     * @see   https://github.com/xp-framework/xp-framework/pull/235
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function loadingClassoneFails() {
      ClassLoader::getDefault()
        ->loadClass('net.xp_framework.unittest.reflection.classes.Classone')
      ;
    }
  }
?>
