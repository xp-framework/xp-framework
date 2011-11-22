<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.DynamicClassLoader');

  /**
   * Tests DynamicClassLoader functionality
   *
   * @see   xp://lang.DynamicClassLoader
   */
  class DynamicClassLoaderTest extends TestCase {
  
    /**
     * Returns fixture
     *
     * @return  lang.DynamicClassLoader
     */
    protected static function fixtureInstance() {
      return DynamicClassLoader::instanceFor(__CLASS__);
    }
  
    /**
     * Defines DynamicClassLoaderTestFixture class
     *
     */
    #[@beforeClass]
    public static function defineClass() {
      self::fixtureInstance()->setClassBytes(
        'net.xp_framework.unittest.core.dyn.DynamicClassLoaderTestFixture', 
        'class DynamicClassLoaderTestFixture extends Object { }'
      );
    }

    /**
     * Test providesPackage()
     *
     */
    #[@test]
    public function doesNotProvideLangPackage() {
      $this->assertFalse(self::fixtureInstance()->providesPackage('lang'));
    }

    /**
     * Test providesPackage()
     *
     */
    #[@test]
    public function providesXPFrameworkUnittestPackage() {
      $this->assertTrue(self::fixtureInstance()->providesPackage('net.xp_framework.unittest'));
    }

    /**
     * Test providesPackage()
     *
     */
    #[@test]
    public function providesCreatedPackage() {
      $this->assertTrue(self::fixtureInstance()->providesPackage('net.xp_framework.unittest.core.dyn'));
    }

    /**
     * Test packageContents()
     *
     */
    #[@test]
    public function xpFrameworkUnittestPackageContents() {
      $this->assertEquals(
        array('core/'),
        self::fixtureInstance()->packageContents('net.xp_framework.unittest')
      );
    }

    /**
     * Test packageContents()
     *
     */
    #[@test]
    public function createdPackagesPackageContents() {
      $this->assertEquals(
        array('DynamicClassLoaderTestFixture.class.php'),
        self::fixtureInstance()->packageContents('net.xp_framework.unittest.core.dyn')
      );
    }
  }
?>
