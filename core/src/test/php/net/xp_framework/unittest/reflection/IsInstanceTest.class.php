<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;


/**
 * TestCase
 *
 * @see      xp://lang.XPClass#isInstance
 */
class IsInstanceTest extends TestCase {

  /**
   * Tests this is a unittest.TestCase
   *
   */
  #[@test]
  public function thisIsATestCase() {
    $this->assertTrue(\lang\XPClass::forName('unittest.TestCase')->isInstance($this));
  }

  /**
   * Tests this is a unittest.TestCase
   *
   */
  #[@test]
  public function thisIsAnInstanceOfThisClass() {
    $this->assertTrue($this->getClass()->isInstance($this));
  }
 
  /**
   * Tests a primitive string is not a lang.types.String
   *
   */
  #[@test]
  public function stringIsNotAString() {
    $this->assertFalse(\lang\XPClass::forName('lang.types.String')->isInstance('Hello'));
  }

  /**
   * Tests lang.Object is not a lang.types.String
   *
   */
  #[@test]
  public function objectIsNotAString() {
    $this->assertFalse(\lang\XPClass::forName('lang.types.String')->isInstance(new \lang\Object()));
  }

  /**
   * Tests lang.Object is a lang.Generic (in fact, any object is)
   *
   */
  #[@test]
  public function objectIsAGeneric() {
    $this->assertTrue(\lang\XPClass::forName('lang.Generic')->isInstance(new \lang\Object()));
  }

  /**
   * Tests lang.Throwable is a lang.Generic (in fact, any object is)
   *
   */
  #[@test]
  public function throwableIsAGeneric() {
    $this->assertTrue(\lang\XPClass::forName('lang.Generic')->isInstance(new \lang\Throwable('')));
  }

  /**
   * Tests an anonymous instance created is of its interface type
   *
   */
  #[@test]
  public function newInterfaceInstanceIsRunnable() {
    $this->assertTrue(\lang\XPClass::forName('lang.Runnable')->isInstance(newinstance('lang.Runnable', array(), '{
      public function run() { }
    }')));
  }

  /**
   * Tests NULL is not instanceof an object
   *
   */
  #[@test]
  public function nullIsNotAnObject() {
    $this->assertFalse(\lang\XPClass::forName('lang.Generic')->isInstance(null));
  }
}
