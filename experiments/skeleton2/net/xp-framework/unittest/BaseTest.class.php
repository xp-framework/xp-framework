<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.profiling.unittest.TestCase');

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class BaseTest extends TestCase {
      
    /**
     * Test prerequisites
     *
     * @access  public
     */
    public function testPrerequisites() {
      self::assertEquals(error_reporting(), E_ALL, 'errorreporting');
      self::assertEquals(get_magic_quotes_gpc(), 0, 'magicquotesgpc');
      self::assertEquals(get_magic_quotes_runtime(), 0, 'magicquotesruntime');
      self::assertIn(version_compare(phpversion(), '4.3.0'), array(0, 1), 'phpversion');
    }
    
    /**
     * Test reflection API via lang.XPClass
     *
     * @access  public
     */
    public function testReflection() {
      $class= XPClass::forName('lang.Exception');
      if (!self::assertClass($class, 'lang.XPClass')) return;
      $parent= $class->getParentClass();
      if (!self::assertClass($parent, 'lang.XPClass')) return;
      $e= $class->newInstance('test');
      if (!self::assertClass($e, 'lang.Exception')) return;
      self::assertEquals($e->message, 'test');
      $o= $parent->newInstance();
      if (!self::assertClass($o, 'lang.Throwable')) return;
    }
  }
?>
