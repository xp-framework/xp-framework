<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String',
    'util.collections.Vector'
  );

  /**
   * TestCase for XP Framework's namespaces support
   *
   * @see   https://github.com/xp-framework/rfc/issues/222
   * @see   xp://net.xp_framework.unittest.core.NamespacedClass
   * @see   php://namespaces
   */
  class NamespacedClassesTest extends TestCase {
    protected static $package= NULL;

    /**
     * Checks for namespaces support
     *
     */
    #[@beforeClass]
    public static function checkForNamespaces() {
      if (version_compare(PHP_VERSION, '5.3.0', 'lt')) {
        throw new PrerequisitesNotMetError('Namespaces not supported', NULL, array('PHP 5.3.0'));
      }
      
      self::$package= Package::forName('net.xp_framework.unittest.core');
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function namespacedClassLiteral() {
      $this->assertEquals(
        'net\\xp_framework\\unittest\\core\\NamespacedClass', 
        self::$package->loadClass('NamespacedClass')->literal()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function packageOfNamespacedClass() {
      $this->assertEquals(
        Package::forName('net.xp_framework.unittest.core'),
        self::$package->loadClass('NamespacedClass')->getPackage()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function namespacedClassUsingUnqualified() {
      $this->assertEquals(
        String::$EMPTY, 
        self::$package->loadClass('NamespacedClassUsingUnqualified')->newInstance()->getEmptyString()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function namespacedClassUsingQualified() {
      $this->assertInstanceOf(
        'net.xp_framework.unittest.core.NamespacedClass',
        self::$package->loadClass('NamespacedClassUsingQualified')->newInstance()->getNamespacedClass()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function namespacedClassUsingQualifiedUnloaded() {
      $this->assertInstanceOf(
        'net.xp_framework.unittest.core.UnloadedNamespacedClass',
        self::$package->loadClass('NamespacedClassUsingQualifiedUnloaded')->newInstance()->getNamespacedClass()
      );
    }

    /**
     * Tests newinstance() on namespaced class
     *
     */
    #[@test]
    public function newInstanceOnNamespacedClass() {
      $i= newinstance('net.xp_framework.unittest.core.NamespacedClass', array(), '{}');
      $this->assertInstanceOf('net.xp_framework.unittest.core.NamespacedClass', $i);
    }

    /**
     * Tests package retrieval on newinstance() created namespaced class
     *
     */
    #[@test]
    public function packageOfNewInstancedNamespacedClass() {
      $i= newinstance('net.xp_framework.unittest.core.NamespacedClass', array(), '{}');
      $this->assertEquals(
        Package::forName('net.xp_framework.unittest.core'),
        $i->getClass()->getPackage()
      );
    }

    /**
     * Tests generics creation
     *
     * @see   https://github.com/xp-framework/xp-framework/issues/132
     */
    #[@test]
    public function generics() {
      $v= create('new util.collections.Vector<net.xp_framework.unittest.core.NamespacedClass>');
      $this->assertTrue($v->getClass()->isGeneric());
    }
  }
?>
