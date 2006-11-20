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
     * @access  public
     */
    #[@test]
    function noConstructor() {
      $c= &XPClass::forName('lang.Object');
      $this->assertFalse($c->hasConstructor());
    }

    /**
     * Ensures lang.Object does not have a parent class
     *
     * @access  public
     */
    #[@test]
    function baseClass() {
      $c= &XPClass::forName('lang.Object');
      $this->assertNull($c->getParentClass());
    }

    /**
     * Ensures lang.Object does not implement any interfaces
     *
     * @access  public
     */
    #[@test]
    function noInterfaces() {
      $c= &XPClass::forName('lang.Object');
      $this->assertEmpty($c->getInterfaces());
    }

    /**
     * Ensures the xp::typeOf() function returns the fully qualified 
     * class name, "lang.Object"
     *
     * @access  public
     */
    #[@test]
    function typeOf() {
      $this->assertEquals('lang.Object', xp::typeOf(new Object()));
    }

    /**
     * Tests the hashCode() method
     *
     * @see     xp://lang.Object#hashCode
     * @access  public
     */
    #[@test]
    function hashCodeMethod() {
      $o= &new Object();
      $this->assertMatches($o->hashCode(), '/^0\.[0-9]+ [0-9]+$/');
    }

    /**
     * Tests the equals() method
     *
     * @see     xp://lang.Object#equals
     * @access  public
     */
    #[@test]
    function equalsMethod() {
      $o= &new Object();
      $this->assertTrue($o->equals($o));
      $this->assertFalse($o->equals(new Object()));
    }
    
    /**
     * Tests the getClassName() method returns the fully qualified
     * class name
     *
     * @see     xp://lang.Object#getClassName
     * @access  public
     */
    #[@test]
    function getClassNameMethod() {
      $o= &new Object();
      $this->assertEquals('lang.Object', $o->getClassName());
    }

    /**
     * Tests the getClassName() method returns the fully qualified
     * class name
     *
     * @see     xp://lang.Object#getClass
     * @access  public
     */
    #[@test]
    function getClassMethod() {
      $o= &new Object();
      $class= &$o->getClass();
      $this->assertClass($class, 'lang.XPClass');
      $this->assertEquals('lang.Object', $class->getName());
    }

    /**
     * Tests the toString() method
     *
     * @see     xp://lang.Object#equals
     * @access  public
     */
    #[@test]
    function toStringMethod() {
      $o= &new Object();
      $this->assertEquals(
        'lang.Object {'."\n".
        '  __id => "'.$o->hashCode().'"'."\n".
        '}', 
        $o->toString()
      );
    }
  }
?>
