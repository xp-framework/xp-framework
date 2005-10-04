<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'util.profiling.unittest.TestCase'
  );

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
    #[@test]
    function testPrerequisites() {
      $this->assertEquals(error_reporting(), E_ALL, 'errorreporting');
      $this->assertEquals(get_magic_quotes_gpc(), 0, 'magicquotesgpc');
      $this->assertEquals(get_magic_quotes_runtime(), 0, 'magicquotesruntime');
      $this->assertEquals(intval(ini_get('session.use_trans_sid')), 0, 'trans_sid');
      $this->assertIn(version_compare(phpversion(), '4.3.0', '>='), array(0, 1), 'phpversion');
    }
    
    /**
     * Test reflection API via lang.XPClass
     *
     * @access  public
     */
    #[@test]
    function testReflection() {
      $class= &XPClass::forName('lang.Exception');
      if (!$this->assertClass($class, 'lang.XPClass')) return;
      $parent= &$class->getParentClass();
      if (!$this->assertClass($parent, 'lang.XPClass')) return;
      $e= &$class->newInstance('test');
      if (!$this->assertClass($e, 'lang.Exception')) return;
      $this->assertEquals($e->message, 'test');
      $o= &$parent->newInstance();
      if (!$this->assertClass($o, 'lang.Throwable')) return;
    }
  }
?>
