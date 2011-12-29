<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String'
  );

  /**
   * TestCase for XP Framework's namespaces support
   *
   * @see   https://github.com/xp-framework/rfc/issues/222
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
  }
?>
