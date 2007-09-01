<?php
/* This class is part of the XP framework
 *
 * $Id: ClassLoaderTest.class.php 10296 2007-05-08 19:22:33Z friebe $ 
 */

  namespace net::xp_framework::unittest::reflection;

  ::uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ClassLoaderTest extends unittest::TestCase {
  
    static function __static() {
      lang::ClassLoader::registerLoader(new lang::archive::ArchiveClassLoader(
        new lang::archive::ArchiveReader(dirname(__FILE__).'/lib/three-and-four.xar')
      ));
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
     * Test "ClassOne" class is loaded from file system
     *
     */
    #[@test]
    public function fileSystemClassLoader() {
      $cl= lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getClassLoader();
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
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getClassLoader(),
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassTwo')->getClassLoader()
      );
    }

    /**
     * Test "ClassThree" is loaded from the archive in "lib"
     *
     */
    #[@test]
    public function archiveClassLoader() {
      $cl= lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree')->getClassLoader();
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
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree')->getClassLoader(),
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassFour')->getClassLoader()
      );
    }

    /**
     * Loads a class that has been loaded before
     *
     */
    #[@test]
    public function loadClass() {
      $this->assertXPClass('lang.Object', lang::ClassLoader::getDefault()->loadClass('lang.Object'));
    }

    /**
     * Tests the findClass() method
     *
     */
    #[@test]
    public function findThisClass() {
      $this->assertEquals(
        $this->getClass()->getClassLoader(),
        lang::ClassLoader::getDefault()->findClass($this->getClassName())
      );
    }

    /**
     * Tests the findClass() method
     *
     */
    #[@test]
    public function findNullClass() {
      $this->assertEquals(::xp::null(), lang::ClassLoader::getDefault()->findClass(NULL));
    }

    /**
     * Loads a class that has *not* been loaded before. Makes sure the
     * static initializer is called.
     *
     */
    #[@test]
    public function initializerCalled() {
      $name= 'net.xp_framework.unittest.reflection.LoaderTestClass';
      if (class_exists(::xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }

      $class= lang::ClassLoader::getDefault()->loadClass($name);
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
      lang::ClassLoader::getDefault()->loadClass('@@NON-EXISTANT@@');
    }
  }
?>
