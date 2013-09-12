<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;
use lang\archive\Archive;


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
    $libraryLoader   = null,
    $brokenLoader    = null,
    $containedLoader = null;
    
  /**
   * Setup this test. Registeres class loaders deleates for the 
   * afforementioned XARs
   *
   */
  public function setUp() {
    $this->libraryLoader= \lang\ClassLoader::registerLoader(new \lang\archive\ArchiveClassLoader(new Archive(\lang\XPClass::forName(\xp::nameOf(__CLASS__))
      ->getPackage()
      ->getPackage('lib')
      ->getResourceAsStream('three-and-four.xar')
    )));
    $this->brokenLoader= \lang\ClassLoader::registerLoader(new \lang\archive\ArchiveClassLoader(new Archive(\lang\XPClass::forName(\xp::nameOf(__CLASS__))
      ->getPackage()
      ->getPackage('lib')
      ->getResourceAsStream('broken.xar')
    )));
    $this->containedLoader= \lang\ClassLoader::registerLoader(new \lang\archive\ArchiveClassLoader(new Archive($this
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
    \lang\ClassLoader::removeLoader($this->libraryLoader);
    \lang\ClassLoader::removeLoader($this->containedLoader);
    \lang\ClassLoader::removeLoader($this->brokenLoader);
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
    with ($p= \lang\reflect\Package::forName('net.xp_framework.unittest.reflection.classes')); {
      \util\cmd\Console::writeLine('Object     : ', \lang\XPClass::forName('lang.Object')->getClassLoader());
      \util\cmd\Console::writeLine('This       : ', $this->getClass()->getClassLoader());
      \util\cmd\Console::writeLine('ClassOne   : ', $p->loadClass('ClassOne')->getClassLoader());
      \util\cmd\Console::writeLine('ClassTwo   : ', $p->loadClass('ClassTwo')->getClassLoader());
      \util\cmd\Console::writeLine('ClassThree : ', $p->loadClass('ClassThree')->getClassLoader());
      \util\cmd\Console::writeLine('ClassFour  : ', $p->loadClass('ClassFour')->getClassLoader());
      \util\cmd\Console::writeLine('ClassFive  : ', $p->loadClass('ClassFive')->getClassLoader());
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
      \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getClassLoader(),
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
      \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getClassLoader(),
      \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassTwo')->getClassLoader()
    );
  }

  /**
   * Test "ClassThree" is loaded from the archive in "lib"
   *
   */
  #[@test]
  public function archiveClassLoader() {
    $this->assertClass(
       \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree')->getClassLoader(),
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
       \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassFive')->getClassLoader(),
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
      \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree')->getClassLoader(),
      \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassFour')->getClassLoader()
    );
  }

  /**
   * Loads a class that has been loaded before
   *
   */
  #[@test]
  public function loadClass() {
    $this->assertXPClass('lang.Object', \lang\ClassLoader::getDefault()->loadClass('lang.Object'));
  }

  /**
   * Tests the findClass() method
   *
   */
  #[@test]
  public function findThisClass() {
    $this->assertEquals(
      $this->getClass()->getClassLoader(),
      \lang\ClassLoader::getDefault()->findClass($this->getClassName())
    );
  }

  /**
   * Tests the findClass() method
   *
   */
  #[@test]
  public function findNullClass() {
    $this->assertEquals(\xp::null(), \lang\ClassLoader::getDefault()->findClass(null));
  }

  /**
   * Loads a class that has *not* been loaded before. Makes sure the
   * static initializer is called.
   *
   */
  #[@test]
  public function initializerCalled() {
    $name= 'net.xp_framework.unittest.reflection.LoaderTestClass';
    if (class_exists(\xp::reflect($name), false)) {
      return $this->fail('Class "'.$name.'" may not exist!');
    }

    $this->assertXPClass($name, \lang\ClassLoader::getDefault()->loadClass($name));
    $this->assertTrue(LoaderTestClass::initializerCalled());
  }

  /**
   * Tests the loadClass() method throws a ClassNotFoundException when given
   * a name of a class that cannot be found. 
   *
   */
  #[@test, @expect('lang.ClassNotFoundException')]
  public function loadNonExistantClass() {
    \lang\ClassLoader::getDefault()->loadClass('@@NON-EXISTANT@@');
  }

  /**
   * Loads a class file that does not declare a class
   *
   */
  #[@test, @expect('lang.ClassFormatException')]
  public function loadClassFileWithoutDeclaration() {
    \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.NoClass');
  }

  /**
   * Loads a class file that does not declare a class
   *
   */
  #[@test, @expect('lang.ClassFormatException')]
  public function loadClassFileWithIncorrectDeclaration() {
    \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.FalseClass');
  }

  /**
   * Loads a class file that loads a file that is broken
   *
   */
  #[@test, @expect('lang.ClassDependencyException')]
  public function loadClassWithBrokenDependency() {
    \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.BrokenDependencyClass');
  }

  /**
   * Loads a class file whose class extends a class that cannot be
   * loaded and the class cannot be declared.
   *
   */
  #[@test, @expect('lang.ClassLinkageException')]
  public function loadClassWithMissingDefinition() {
    \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.broken.MissingDefinitionClass');
  }

  /**
   * Test
   *
   */
  #[@test]
  public function loadClassFileWithRecusionInStaticBlock() {
    with ($p= \lang\reflect\Package::forName('net.xp_framework.unittest.reflection.classes')); {
      $two= $p->loadClass('StaticRecursionTwo');
      $one= $p->loadClass('StaticRecursionOne');
      $this->assertEquals($two, $one->getField('two')->get(null));
    }
  }

  /**
   * Test (mis-)using the public constructor does not lead to a 
   * ReflectionException surfacing
   *
   */
  #[@test, @expect('lang.IllegalStateException')]
  public function newInstance() {
    new \lang\XPClass('DoesNotExist');
  }

  /**
   * Test (mis-)using the public constructor does not lead to a 
   * ReflectionException surfacing
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function newInstance__PHP_Incomplete_Class() {
    new \lang\XPClass(unserialize('O:12:"DoesNotExist":0:{}'));
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
    $this->assertFalse(\lang\ClassLoader::getDefault()
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
    \lang\ClassLoader::getDefault()
      ->loadClass('net.xp_framework.unittest.reflection.classes.Classone')
    ;
  }

  /**
   * Test providesUri()
   *
   */
  #[@test]
  public function providesExistantUri() {
    $this->assertTrue(
      \lang\ClassLoader::getDefault()->providesUri('net/xp_framework/unittest/reflection/classes/ClassOne.class.php')
    );
  }

  /**
   * Test providesUri()
   *
   */
  #[@test]
  public function doesNotProvideNonExistantUri() {
    $this->assertFalse(
      \lang\ClassLoader::getDefault()->providesUri('non/existant/Class.class.php')
    );
  }

  /**
   * Test findUri()
   *
   */
  #[@test]
  public function findExistantUri() {
    $cl= \lang\ClassLoader::getDefault();
    $this->assertEquals(
      $cl->findClass('net.xp_framework.unittest.reflection.classes.ClassOne'),
      $cl->findUri('net/xp_framework/unittest/reflection/classes/ClassOne.class.php')
    );
  }

  /**
   * Test findUri()
   *
   */
  #[@test]
  public function cannotFindNontExistantUri() {
    $this->assertEquals(
      \xp::null(),
      \lang\ClassLoader::getDefault()->findUri('non/existant/Class.class.php')
    );
  }

  /**
   * Test loadUri()
   *
   */
  #[@test]
  public function loadUri() {
    $this->assertEquals(
      \lang\XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne'),
      \lang\ClassLoader::getDefault()->loadUri('net/xp_framework/unittest/reflection/classes/ClassOne.class.php')
    );
  }

  /**
   * Test loadUri()
   *
   */
  #[@test, @expect('lang.ClassNotFoundException')]
  public function loadNonExistantUri() {
    \lang\ClassLoader::getDefault()->loadUri('non/existant/Class.class.php');
  }
}
