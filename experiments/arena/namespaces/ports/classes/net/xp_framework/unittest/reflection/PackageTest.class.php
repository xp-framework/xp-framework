<?php
/* This class is part of the XP framework
 *
 * $Id: PackageTest.class.php 11040 2007-09-06 15:37:00Z friebe $ 
 */

  namespace net::xp_framework::unittest::reflection;

  ::uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class PackageTest extends unittest::TestCase {
    protected static
      $testClasses= array(
        'ClassOne', 'ClassTwo', 'RecursionOne', 'RecursionTwo',   // Filesystem
        'ClassThree', 'ClassFour',                                // XAR
      ),
      $testPackages= array(
        'classes', 'lib'
      );

    static function __static() {
      lang::ClassLoader::registerLoader(new lang::archive::ArchiveClassLoader(
        new lang::archive::ArchiveReader(dirname(__FILE__).'/lib/three-and-four.xar')
      ));
    }

    /**
     * Tests the getName() method
     *
     */
    #[@test]
    public function packageName() {
      $this->assertEquals(
        'net.xp_framework.unittest.reflection.classes', 
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes')->getName()
      );
    }

    /**
     * Tests forName() throws an ElementNotFoundException
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonExistantPackage() {
      lang::reflect::Package::forName('@@non-existant-package@@');
    }

    /**
     * Tests all test classes are provided by the "net.xp_framework.unittest.reflection.classes" package
     *
     */
    #[@test]
    public function providesTestClasses() {
      $p= lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes');
      foreach (self::$testClasses as $name) {
        $this->assertTrue($p->providesClass($name), $name);
      }
    }

    /**
     * Tests class loading
     *
     */
    #[@test]
    public function loadClassByName() {
      $this->assertEquals(
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne'),
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes')->loadClass('ClassOne')
      );
    }

    /**
     * Tests class loading
     *
     */
    #[@test]
    public function loadClassByQualifiedName() {
      $this->assertEquals(
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassThree'),
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes')->loadClass('net.xp_framework.unittest.reflection.classes.ClassThree')
      );
    }

    /**
     * Tests class loading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function loadClassFromDifferentPackage() {
      lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes')->loadClass('lang.reflect.Method');
    }

    /**
     * Tests XPClass::getPackage() method
     *
     */
    #[@test]
    public function classPackage() {
      $this->assertEquals(
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes'),
        lang::XPClass::forName('net.xp_framework.unittest.reflection.classes.ClassOne')->getPackage()
      );
    }

    /**
     * Tests providesClass() method
     *
     */
    #[@test]
    public function doesNotProvideNonExistantClass() {
      $this->assertFalse(lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes')->providesClass('@@non-existant-class@@'));
    }

    /**
     * Tests the getClassNames() method
     *
     */
    #[@test]
    public function getTestClassNames() {
      $base= 'net.xp_framework.unittest.reflection.classes';
      $names= lang::reflect::Package::forName($base)->getClassNames();
      $this->assertEquals(sizeof(self::$testClasses), sizeof($names), ::xp::stringOf($names));
      foreach ($names as $name) {
        $this->assertTrue(
          in_array(substr($name, strlen($base)+ 1), self::$testClasses), 
          $name
        );
      }
    }

    /**
     * Tests the getClasses() method
     *
     */
    #[@test]
    public function getTestClasses() {
      $base= 'net.xp_framework.unittest.reflection.classes';
      $classes= lang::reflect::Package::forName($base)->getClasses();
      $this->assertEquals(sizeof(self::$testClasses), sizeof($classes), ::xp::stringOf($classes));
      foreach ($classes as $class) {
        $this->assertTrue(
          in_array(substr($class->getName(), strlen($base)+ 1), self::$testClasses), 
          $class->getName()
        );
      }
    }

    /**
     * Tests the getPackageNames() method
     *
     */
    #[@test]
    public function getPackageNames() {
      $base= 'net.xp_framework.unittest.reflection';
      $names= lang::reflect::Package::forName($base)->getPackageNames();
      $this->assertEquals(sizeof(self::$testPackages), sizeof($names), ::xp::stringOf($names));
      foreach ($names as $name) {
        $this->assertTrue(
          in_array(substr($name, strlen($base)+ 1), self::$testPackages), 
          $name
        );
      }
    }

    /**
     * Tests the getPackages() method
     *
     */
    #[@test]
    public function getPackages() {
      $base= 'net.xp_framework.unittest.reflection';
      $packages= lang::reflect::Package::forName($base)->getPackages();
      $this->assertEquals(sizeof(self::$testPackages), sizeof($packages), ::xp::stringOf($packages));
      foreach ($packages as $package) {
        $this->assertTrue(
          in_array(substr($package->getName(), strlen($base)+ 1), self::$testPackages), 
          $package->getName()
        );
      }
    }

    /**
     * Tests the getPackage() method
     *
     */
    #[@test]
    public function loadPackageByName() {
      $this->assertEquals(
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes'),
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection')->getPackage('classes')
      );
    }

    /**
     * Tests the getPackage() method
     *
     */
    #[@test]
    public function loadPackageByQualifiedName() {
      $this->assertEquals(
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection.classes'),
        lang::reflect::Package::forName('net.xp_framework.unittest.reflection')->getPackage('net.xp_framework.unittest.reflection.classes')
      );
    }

    /**
     * Tests the getPackage() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function loadPackageByDifferentName() {
      lang::reflect::Package::forName('net.xp_framework.unittest.reflection')->getPackage('lang.reflect');
    }
  }
?>
