<?php
/* This class is part of the XP framework
 *
 * $Id: ObjectTest.class.php 10447 2007-05-29 11:14:31Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses('unittest.TestCase');

  /**
   * Tests the lang.Object class
   *
   * @see      xp://lang.Object
   * @purpose  Testcase
   */
  class ObjectTest extends unittest::TestCase {

    /**
     * Ensures lang.Object does not have a constructor
     *
     */
    #[@test]
    public function noConstructor() {
      $this->assertFalse(lang::XPClass::forName('lang.Object')->hasConstructor());
    }

    /**
     * Ensures lang.Object does not have a parent class
     *
     */
    #[@test]
    public function baseClass() {
      $this->assertNull(lang::XPClass::forName('lang.Object')->getParentClass());
    }

    /**
     * Ensures lang.Object implements the lang.Generic interface
     *
     */
    #[@test]
    public function genericInterface() {
      $interfaces= lang::XPClass::forName('lang.Object')->getInterfaces();
      $this->assertEquals(1, sizeof($interfaces));
      $this->assertClass($interfaces[0], 'lang.XPClass');
      $this->assertEquals('lang.Generic', $interfaces[0]->getName());
    }

    /**
     * Ensures the xp::typeOf() function returns the fully qualified 
     * class name, "lang.Object"
     *
     */
    #[@test]
    public function typeOf() {
      $this->assertEquals('lang.Object', ::xp::typeOf(new lang::Object()));
    }

    /**
     * Tests the hashCode() method
     *
     * @see     xp://lang.Object#hashCode
     */
    #[@test]
    public function hashCodeMethod() {
      $o= new lang::Object();
      $this->assertTrue((bool)preg_match('/^0\.[0-9]+ [0-9]+$/', $o->hashCode()));
    }

    /**
     * Tests the equals() method
     *
     * @see     xp://lang.Object#equals
     */
    #[@test]
    public function equalsMethod() {
      $o= new lang::Object();
      $this->assertTrue($o->equals($o));
      $this->assertFalse($o->equals(new lang::Object()));
    }
    
    /**
     * Tests the getClassName() method returns the fully qualified
     * class name
     *
     * @see     xp://lang.Object#getClassName
     */
    #[@test]
    public function getClassNameMethod() {
      $o= new lang::Object();
      $this->assertEquals('lang.Object', $o->getClassName());
    }

    /**
     * Tests the getClassName() method returns the fully qualified
     * class name
     *
     * @see     xp://lang.Object#getClass
     */
    #[@test]
    public function getClassMethod() {
      $o= new lang::Object();
      $class= $o->getClass();
      $this->assertClass($class, 'lang.XPClass');
      $this->assertEquals('lang.Object', $class->getName());
    }

    /**
     * Tests the toString() method
     *
     * @see     xp://lang.Object#equals
     */
    #[@test]
    public function toStringMethod() {
      $o= new lang::Object();
      $this->assertEquals(
        'lang.Object {'."\n".
        '  __id => "'.$o->hashCode().'"'."\n".
        '}', 
        $o->toString()
      );
    }
  }
?>
