<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\types\String;
use lang\reflect\Package;
use util\collections\Vector;

/**
 * TestCase for XP Framework's namespaces support
 *
 * @see   https://github.com/xp-framework/xp-framework/issues/132
 * @see   https://github.com/xp-framework/rfc/issues/222
 * @see   xp://net.xp_framework.unittest.core.NamespacedClass
 * @see   php://namespaces
 */
class NamespacedClassesTest extends TestCase {
  protected static $package= null;

  /**
   * Initializes package member
   */
  #[@beforeClass]
  public static function initializePackage() {
    self::$package= Package::forName('net.xp_framework.unittest.core');
  }

  #[@test]
  public function namespacedClassLiteral() {
    $this->assertEquals(
      'net\\xp_framework\\unittest\\core\\NamespacedClass', 
      self::$package->loadClass('NamespacedClass')->literal()
    );
  }

  #[@test]
  public function packageOfNamespacedClass() {
    $this->assertEquals(
      Package::forName('net.xp_framework.unittest.core'),
      self::$package->loadClass('NamespacedClass')->getPackage()
    );
  }

  #[@test]
  public function namespacedClassUsingUnqualified() {
    $this->assertEquals(
      String::$EMPTY, 
      self::$package->loadClass('NamespacedClassUsingUnqualified')->newInstance()->getEmptyString()
    );
  }

  #[@test]
  public function namespacedClassUsingQualified() {
    $this->assertInstanceOf(
      'net.xp_framework.unittest.core.NamespacedClass',
      self::$package->loadClass('NamespacedClassUsingQualified')->newInstance()->getNamespacedClass()
    );
  }

  #[@test]
  public function namespacedClassUsingQualifiedUnloaded() {
    $this->assertInstanceOf(
      'net.xp_framework.unittest.core.UnloadedNamespacedClass',
      self::$package->loadClass('NamespacedClassUsingQualifiedUnloaded')->newInstance()->getNamespacedClass()
    );
  }

  #[@test]
  public function newInstanceOnNamespacedClass() {
    $i= newinstance('net.xp_framework.unittest.core.NamespacedClass', array(), '{}');
    $this->assertInstanceOf('net.xp_framework.unittest.core.NamespacedClass', $i);
  }

  #[@test]
  public function packageOfNewInstancedNamespacedClass() {
    $i= newinstance('net.xp_framework.unittest.core.NamespacedClass', array(), '{}');
    $this->assertEquals(
      \lang\reflect\Package::forName('net.xp_framework.unittest.core'),
      $i->getClass()->getPackage()
    );
  }

  #[@test]
  public function generics() {
    $v= create('new util.collections.Vector<net.xp_framework.unittest.core.NamespacedClass>');
    $this->assertTrue($v->getClass()->isGeneric());
  }
}
