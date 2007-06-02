<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.reflection.TestClass'
  );

  /**
   * Tests invoking class "internas" - private & protected methods.
   *
   * @ext      runkit
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Routine
   * @purpose  Unittest
   */
  class InternaInvocationTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      if (extension_loaded('runkit')) {
        throw new PrerequisitesNotMetError('Runtime method injection not enabled', 'library.missing:runkit');
      }
      $this->class= XPClass::forName('net.xp_framework.unittest.reflection.TestClass');
    }
    
    /**
     * Tests invoking protected TestClass::clearMap()
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test]
    public function invokeProtected() {
      $i= $this->class->newInstance();
      $i->setMap(array('foo' => 'bar'));
      $this->class->getMethod('clearMap')->setAccessible()->invoke($i);
      $this->assertEquals(array(), $i->getMap());
    }

    /**
     * Tests invoking private TestClass::defaultMap()
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test]
    public function invokePrivate() {
      $i= $this->class->newInstance();
      $this->class->getMethod('defaultMap')->setAccessible()->invoke($i);
      $this->assertEquals(array('binford' => 61), $i->getMap());
    }

    /**
     * Tests invoking private TestClass::defaultMap() without using 
     * setAccessible() first.
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokePrivateWithoutSettingAccessible() {
      $this->class->getMethod('defaultMap')->invoke($this->class->newInstance());
    }
    
  }
?>
