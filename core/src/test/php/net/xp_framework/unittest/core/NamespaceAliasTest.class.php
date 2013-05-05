<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the XP Framework's optional namespace support
   *
   */
  class NamespaceAliasTest extends TestCase {

    /**
     * Tests lang.Object
     *
     */
    #[@test]
    public function lang_object_class_exists() {
      $this->assertTrue(class_exists('lang\Object', FALSE));
    }

    /**
     * Tests lang.Generic
     *
     */
    #[@test]
    public function lang_generic_interface_exists() {
      $this->assertTrue(interface_exists('lang\Generic', FALSE));
    }

    /**
     * Tests unittest.TestCase
     *
     */
    #[@test]
    public function unittest_testcase_class_exists() {
      $this->assertTrue(class_exists('unittest\TestCase', FALSE));
    }

    /**
     * Tests ClassLoader::defineClass()
     *
     */
    #[@test]
    public function defined_class_exists() {
      ClassLoader::defineClass(
        'net.xp_framework.unittest.core.NamespaceAliasClassFixture', 
        'lang.Object', 
        array(), 
        '{}'
      );
      $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasClassFixture', FALSE));
    }

    /**
     * Tests ClassLoader::defineClass()
     *
     */
    #[@test]
    public function defined_interface_exists() {
      ClassLoader::defineInterface(
        'net.xp_framework.unittest.core.NamespaceAliasInterfaceFixture', 
        array(), 
        '{}'
      );
      $this->assertTrue(interface_exists('net\xp_framework\unittest\core\NamespaceAliasInterfaceFixture', FALSE));
    }

    /**
     * Tests autoload functionality with locally qualified class
     *
     */
    #[@test]
    public function autoloaded_class_exists() {
      new \net\xp_framework\unittest\core\NamespaceAliasAutoloadedFixture();    // Triggers autoloader
      $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasAutoloadedFixture', FALSE));
    }

    /**
     * Tests autoload functionality with namespaced class
     *
     */
    #[@test]
    public function autoloaded_namespaced_class_exists() {
      new \net\xp_framework\unittest\core\NamespaceAliasAutoloadedNamespacedFixture();    // Triggers autoloader
      $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasAutoloadedNamespacedFixture', FALSE));
    }

    /**
     * Tests autoload functionality with fully qualified class
     *
     */
    #[@test]
    public function autoloaded_fq_class_exists() {
      new \net\xp_framework\unittest\core\NamespaceAliasAutoloadedFQFixture();    // Triggers autoloader
      $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasAutoloadedFQFixture', FALSE));
    }
  }
?>
