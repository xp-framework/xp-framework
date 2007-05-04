<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class PackageTest extends TestCase {
    protected static
      $testClasses= array('ClassOne', 'ClassTwo', 'ClassThree', 'ClassFour');

    static function __static() {
      ClassLoader::registerLoader(new ArchiveClassLoader(
        new ArchiveReader(dirname(__FILE__).'/lib/three-and-four.xar')
      ));
    }

    /**
     * Tests the getName() method
     *
     */
    #[@test]
    public function packageName() {
      $this->assertEquals('tests.classes', Package::forName('tests.classes')->getName());
    }

    /**
     * Tests all test classes are provided by the "tests.classes" package
     *
     */
    #[@test]
    public function providesTestClasses() {
      $p= Package::forName('tests.classes');
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
        XPClass::forName('tests.classes.ClassOne'),
        Package::forName('tests.classes')->loadClass('ClassOne')
      );
    }

    /**
     * Tests class loading
     *
     */
    #[@test]
    public function loadClassByQualifiedName() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassThree'),
        Package::forName('tests.classes')->loadClass('tests.classes.ClassThree')
      );
    }

    /**
     * Tests class loading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function loadClassFromDifferentPackage() {
      Package::forName('tests.classes')->loadClass('lang.reflect.Method');
    }

    /**
     * Tests providesClass() method
     *
     */
    #[@test]
    public function doesNotProvideNonExistantClass() {
      $this->assertFalse(Package::forName('tests.classes')->providesClass('@@non-existant-class@@'));
    }

    /**
     * Tests the getClassNames() method
     *
     */
    #[@test]
    public function getTestClassNames() {
      $names= Package::forName('tests.classes')->getClasses();
      $this->assertEquals(sizeof(self::$testClasses), sizeof($names));
      foreach ($classes as $class) {
        $this->assertTrue(in_array($names, self::$testClasses), $names);
      }
    }

    /**
     * Tests the getClasses() method
     *
     */
    #[@test]
    public function getTestClasses() {
      $classes= Package::forName('tests.classes')->getClasses();
      $this->assertEquals(sizeof(self::$testClasses), sizeof($classes));
      foreach ($classes as $class) {
        $this->assertTrue(in_array($class->getName(), self::$testClasses), $class);
      }
    }
  }
?>
