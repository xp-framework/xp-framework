<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the lang.Object class
   *
   * @see      xp://lang.Object
   * @purpose  Testcase
   */
  class ObjectTest extends TestCase {

    /**
     * Ensures lang.Object does not have a constructor
     *
     */
    #[@test]
    public function noConstructor() {
      $this->assertFalse(XPClass::forName('lang.Object')->hasConstructor());
    }

    /**
     * Ensures lang.Object does not have a parent class
     *
     */
    #[@test]
    public function baseClass() {
      $this->assertNull(XPClass::forName('lang.Object')->getParentClass());
    }

    /**
     * Ensures lang.Object implements the lang.Generic interface
     *
     */
    #[@test]
    public function genericInterface() {
      $interfaces= XPClass::forName('lang.Object')->getInterfaces();
      $this->assertEquals(1, sizeof($interfaces));
      $this->assertInstanceOf('lang.XPClass', $interfaces[0]);
      $this->assertEquals('lang.Generic', $interfaces[0]->getName());
    }

    /**
     * Ensures the xp::typeOf() function returns the fully qualified 
     * class name, "lang.Object"
     *
     */
    #[@test]
    public function typeOf() {
      $this->assertEquals('lang.Object', xp::typeOf(new Object()));
    }

    /**
     * Tests the hashCode() method
     *
     * @see     xp://lang.Object#hashCode
     */
    #[@test]
    public function hashCodeMethod() {
      $o= new Object();
      $this->assertTrue((bool)preg_match('/^0\.[0-9]+ [0-9]+$/', $o->hashCode()));
    }

    /**
     * Tests the equals() method
     *
     * @see     xp://lang.Object#equals
     */
    #[@test]
    public function objectIsEqualToSelf() {
      $o= new Object();
      $this->assertTrue($o->equals($o));
    }

    /**
     * Tests the equals() method
     *
     * @see     xp://lang.Object#equals
     */
    #[@test]
    public function objectIsNotEqualToOtherObject() {
      $o= new Object();
      $this->assertFalse($o->equals(new Object()));
    }

    /**
     * Tests the equals() method
     *
     * @see     xp://lang.Object#equals
     */
    #[@test]
    public function objectIsNotEqualToPrimitive() {
      $o= new Object();
      $this->assertFalse($o->equals(0));
    }
    
    /**
     * Tests the getClassName() method returns the fully qualified
     * class name
     *
     * @see     xp://lang.Object#getClassName
     */
    #[@test]
    public function getClassNameMethod() {
      $o= new Object();
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
      $o= new Object();
      $class= $o->getClass();
      $this->assertInstanceOf('lang.XPClass', $class);
      $this->assertEquals('lang.Object', $class->getName());
    }

    /**
     * Tests the toString() method
     *
     * @see     xp://lang.Object#equals
     */
    #[@test]
    public function toStringMethod() {
      $o= new Object();
      $this->assertEquals(
        'lang.Object {'."\n".
        '  __id => "'.$o->hashCode().'"'."\n".
        '}', 
        $o->toString()
      );
    }
  }
?>
