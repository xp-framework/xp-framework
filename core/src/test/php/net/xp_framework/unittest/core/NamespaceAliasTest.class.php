<?php namespace net\xp_framework\unittest\core;

use lang\ClassLoader;

/**
 * Tests the XP Framework's optional namespace support
 */
class NamespaceAliasTest extends \unittest\TestCase {

  #[@test]
  public function lang_object_class_exists() {
    $this->assertTrue(class_exists('lang\Object', false));
  }

  #[@test]
  public function lang_generic_interface_exists() {
    $this->assertTrue(interface_exists('lang\Generic', false));
  }

  #[@test]
  public function unittest_testcase_class_exists() {
    $this->assertTrue(class_exists('unittest\TestCase', false));
  }

  #[@test]
  public function defined_class_exists() {
    ClassLoader::defineClass(
      'net.xp_framework.unittest.core.NamespaceAliasClassFixture', 
      'lang.Object', 
      array(), 
      '{}'
    );
    $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasClassFixture', false));
  }

  #[@test]
  public function defined_interface_exists() {
    ClassLoader::defineInterface(
      'net.xp_framework.unittest.core.NamespaceAliasInterfaceFixture', 
      array(), 
      '{}'
    );
    $this->assertTrue(interface_exists('net\xp_framework\unittest\core\NamespaceAliasInterfaceFixture', false));
  }

  #[@test]
  public function autoloaded_class_exists() {
    new NamespaceAliasAutoloadedFixture();              // Triggers autoloader
    $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasAutoloadedFixture', false));
  }

  #[@test]
  public function autoloaded_namespaced_class_exists() {
    new NamespaceAliasAutoloadedNamespacedFixture();    // Triggers autoloader
    $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasAutoloadedNamespacedFixture', false));
  }

  #[@test]
  public function autoloaded_fq_class_exists() {
    new NamespaceAliasAutoloadedFQFixture();            // Triggers autoloader
    $this->assertTrue(class_exists('net\xp_framework\unittest\core\NamespaceAliasAutoloadedFQFixture', false));
  }

  #[@test]
  public function namespaced_classes_aliased_to_short_name_when_loaded_via_uses() {
    uses('net.xp_framework.unittest.core.NamespaceAliasAutoloadedNamespacedFixture');
    $this->assertTrue(class_exists('NamespaceAliasAutoloadedNamespacedFixture', false));
  }
}
